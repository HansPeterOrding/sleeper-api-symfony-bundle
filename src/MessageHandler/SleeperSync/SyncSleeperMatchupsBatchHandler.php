<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientInterface;
use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperMatchupsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayoffMatchupsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Service\SleeperImportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class SyncSleeperMatchupsBatchHandler
{
    public function __construct(
        private readonly Connection                $db,
        private readonly SleeperApiClientInterface $apiClient,
        private readonly MessageBusInterface       $messageBus,
        private readonly SleeperImportService      $importService,
        private readonly LoggerInterface           $logger,
    )
    {
    }

    public function __invoke(SyncSleeperMatchupsBatchMessage $message): void
    {
        if ($message->matchups === []) {
            return;
        }

        $this->db->beginTransaction();
        try {
            $internalLeagueId = $this->fetchInternalLeagueId($message->leagueId);
            $rosterIdMap = $this->fetchRosterMap($message->leagueId);

            $rows = [];
            foreach ($message->matchups as $week => $weeklyMatchups) {
                foreach ($weeklyMatchups as $matchup) {
                    $rosterId = $matchup->getRosterId();
                    if (!$rosterId || !isset($rosterIdMap[$rosterId])) {
                        continue;
                    }

                    $rows[] = [
                        'internal_league_id' => $internalLeagueId,
                        'internal_roster_id' => $rosterIdMap[$rosterId],
                        'league_id' => $message->leagueId,
                        'week' => (int)$week,
                        'starters_points' => $this->toJsonText($matchup->getStartersPoints()),
                        'players_points' => $this->toJsonText($matchup->getPlayersPoints()),
                        'starters' => $this->toJsonText($matchup->getStarters()),
                        'roster_id' => $rosterId,
                        'players' => $this->toJsonText($matchup->getPlayers()),
                        'matchup_id' => $matchup->getMatchupId(),
                        'points' => $matchup->getPoints(),
                        'custom_points' => $matchup->getCustomPoints(),
                    ];
                }
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                $this->bulkUpsertMatchups($chunk);
            }

            // Junction tables
            $weekToRosterIds = [];
            foreach ($rows as $r) {
                $weekToRosterIds[(int)$r['week']][] = (int)$r['roster_id'];
            }
            foreach ($weekToRosterIds as $w => $list) {
                $weekToRosterIds[$w] = array_values(array_unique($list));
            }

            if ($weekToRosterIds !== []) {
                $matchupPkMapByWeek = $this->fetchMatchupPkMapByWeek($message->leagueId, $weekToRosterIds);
                [$allExtPids, $startersByMid, $playersByMid] = $this->collectPlayersForJunctions(
                    $message->matchups, $matchupPkMapByWeek
                );

                $playerIdMap = $this->fetchPlayerIdMap($allExtPids);
                $matchupDbIds = array_keys($startersByMid + $playersByMid);

                if ($matchupDbIds !== []) {
                    $this->wipeJunctions($matchupDbIds);

                    $starterRows = [];
                    foreach ($startersByMid as $mid => $extPids) {
                        foreach ($extPids as $pid) {
                            if (isset($playerIdMap[$pid])) {
                                $starterRows[] = ['matchup_id' => (int)$mid, 'player_id' => $playerIdMap[$pid]];
                            }
                        }
                    }
                    foreach (array_chunk($starterRows, 1000) as $chunk) {
                        $this->bulkInsertJunction('public.sasb_matchup_starters', $chunk);
                    }

                    $playerRows = [];
                    foreach ($playersByMid as $mid => $extPids) {
                        foreach ($extPids as $pid) {
                            if (isset($playerIdMap[$pid])) {
                                $playerRows[] = ['matchup_id' => (int)$mid, 'player_id' => $playerIdMap[$pid]];
                            }
                        }
                    }
                    foreach (array_chunk($playerRows, 1000) as $chunk) {
                        $this->bulkInsertJunction('public.sasb_matchup_players', $chunk);
                    }
                }
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->logger->warning('SyncSleeperMatchupsBatchHandler transient error', [
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        // Dispatch playoff matchups if flag is set — runs after commit so matchups are guaranteed
        $importEntities = $message->importEntities;
        if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_PLAYOFF_MATCHUPS)) {
            $winnersData = $this->apiClient->league()->listPlayoffMatchups(
                $message->leagueId, AbstractEndpoint::BRANCH_WINNERS
            ) ?? [];
            $losersData = $this->apiClient->league()->listPlayoffMatchups(
                $message->leagueId, AbstractEndpoint::BRANCH_LOSERS
            ) ?? [];

            if ($winnersData !== [] || $losersData !== []) {
                $this->messageBus->dispatch(new SyncSleeperPlayoffMatchupsBatchMessage(
                    leagueId: $message->leagueId,
                    winnersData: $winnersData,
                    losersData: $losersData,
                ));
            }
        }
    }

    private function fetchInternalLeagueId(string $leagueId): int
    {
        $id = $this->db->fetchOne(
            'SELECT id FROM public.sasb_sleeper_league WHERE league_id = ?',
            [$leagueId]
        );
        if (!$id) {
            throw new \RuntimeException("League {$leagueId} not found.");
        }
        return (int)$id;
    }

    /** @return array<int,int> [roster_id => internal id] */
    private function fetchRosterMap(string $leagueId): array
    {
        $rows = $this->db->fetchAllAssociative(
            'SELECT roster_id, id FROM public.sasb_sleeper_roster WHERE league_id = ?',
            [$leagueId]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['roster_id']] = (int)$r['id'];
        }
        return $map;
    }

    private function bulkUpsertMatchups(array $chunk): void
    {
        if ($chunk === []) return;

        $tuple = '(' . implode(',', ['?', '?', '?', '?', '?::json', '?::json', '?::json', '?', '?::json', '?', '?', '?']) . ')';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_matchup
  (internal_league_id, internal_roster_id, league_id, week,
   starters_points, players_points, starters, roster_id,
   players, matchup_id, points, custom_points)
VALUES {$valuesSql}
ON CONFLICT (league_id, week, roster_id)
DO UPDATE SET
  internal_league_id = EXCLUDED.internal_league_id,
  internal_roster_id = EXCLUDED.internal_roster_id,
  starters_points    = EXCLUDED.starters_points,
  players_points     = EXCLUDED.players_points,
  starters           = EXCLUDED.starters,
  players            = EXCLUDED.players,
  matchup_id         = EXCLUDED.matchup_id,
  points             = EXCLUDED.points,
  custom_points      = EXCLUDED.custom_points
SQL;

        $params = [];
        foreach ($chunk as $r) {
            $params[] = $r['internal_league_id'];
            $params[] = $r['internal_roster_id'];
            $params[] = $r['league_id'];
            $params[] = $r['week'];
            $params[] = $r['starters_points'];
            $params[] = $r['players_points'];
            $params[] = $r['starters'];
            $params[] = $r['roster_id'];
            $params[] = $r['players'];
            $params[] = $r['matchup_id'];
            $params[] = $r['points'];
            $params[] = $r['custom_points'];
        }

        $this->db->executeStatement($sql, $params);
    }

    /** @return array<int,array<int,int>> [week => [roster_id => matchup_db_id]] */
    private function fetchMatchupPkMapByWeek(string $leagueId, array $weekToRosterIds): array
    {
        $out = [];
        foreach ($weekToRosterIds as $week => $rosterIds) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT id, roster_id FROM public.sasb_sleeper_matchup WHERE league_id = ? AND week = ? AND roster_id IN (?)',
                [$leagueId, $week, $rosterIds],
                [ParameterType::STRING, ParameterType::INTEGER, ArrayParameterType::INTEGER]
            );
            $map = [];
            foreach ($rows as $r) {
                $map[(int)$r['roster_id']] = (int)$r['id'];
            }
            $out[(int)$week] = $map;
        }
        return $out;
    }

    private function collectPlayersForJunctions(array $matchupsByWeek, array $pkMapByWeek): array
    {
        $all = [];
        $startersByMid = [];
        $playersByMid = [];

        foreach ($matchupsByWeek as $week => $weekly) {
            $rosterToMid = $pkMapByWeek[(int)$week] ?? [];
            foreach ($weekly as $matchup) {
                $rid = (int)$matchup->getRosterId();
                if (!$rid || !isset($rosterToMid[$rid])) continue;
                $mid = $rosterToMid[$rid];

                $st = array_values(array_filter(array_map('strval', $matchup->getStarters() ?? [])));
                $pl = array_values(array_filter(array_map('strval', $matchup->getPlayers() ?? [])));

                if ($st) {
                    $startersByMid[$mid] = $st;
                    foreach ($st as $p) $all[$p] = true;
                }
                if ($pl) {
                    $playersByMid[$mid] = $pl;
                    foreach ($pl as $p) $all[$p] = true;
                }
            }
        }
        return [array_keys($all), $startersByMid, $playersByMid];
    }

    private function fetchPlayerIdMap(array $externalPids): array
    {
        $externalPids = array_values(array_unique(array_filter(array_map('strval', $externalPids))));
        if ($externalPids === []) return [];
        $map = [];
        foreach (array_chunk($externalPids, 1000) as $chunk) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT player_id, id FROM public.sasb_sleeper_player WHERE player_id IN (?)',
                [$chunk], [ArrayParameterType::STRING]
            );
            foreach ($rows as $r) {
                $map[(string)$r['player_id']] = (int)$r['id'];
            }
        }
        return $map;
    }

    private function wipeJunctions(array $matchupDbIds): void
    {
        $ids = array_values(array_unique(array_map('intval', $matchupDbIds)));
        if ($ids === []) return;
        foreach (array_chunk($ids, 1000) as $chunk) {
            $this->db->executeStatement('DELETE FROM public.sasb_matchup_starters WHERE matchup_id IN (?)', [$chunk], [ArrayParameterType::INTEGER]);
            $this->db->executeStatement('DELETE FROM public.sasb_matchup_players WHERE matchup_id IN (?)', [$chunk], [ArrayParameterType::INTEGER]);
        }
    }

    private function bulkInsertJunction(string $table, array $rows): void
    {
        if ($rows === []) return;
        $values = implode(',', array_fill(0, count($rows), '(?,?)'));
        $sql = "INSERT INTO {$table} (matchup_id, player_id) VALUES {$values} ON CONFLICT DO NOTHING";
        $params = [];
        foreach ($rows as $r) {
            $params[] = $r['matchup_id'];
            $params[] = $r['player_id'];
        }
        $this->db->executeStatement($sql, $params);
    }

    private function toJsonText(mixed $value): ?string
    {
        if ($value === null) return null;
        if (is_string($value)) {
            $t = ltrim($value);
            if ($t !== '' && ($t[0] === '{' || $t[0] === '[')) return $t;
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
