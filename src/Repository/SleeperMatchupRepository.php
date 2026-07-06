<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup as SleeperMatchupDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperMatchup as SleeperMatchupEntity;

/**
 * @method SleeperMatchupEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperMatchupEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperMatchupEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperMatchupEntity[] findAll()
 */
class SleeperMatchupRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

    public function __construct(
        ManagerRegistry                          $registry,
        private readonly SleeperLeagueRepository $sleeperLeagueRepository,
        private readonly SleeperRosterRepository $sleeperRosterRepository,
        private readonly SleeperPlayerRepository $sleeperPlayerRepository,
    )
    {
        parent::__construct($registry, SleeperMatchupEntity::class);
    }

    public function findByDtoOrCreateEntity(string $leagueId, int $week, SleeperMatchupDto $sleeperMatchupDto): SleeperMatchupEntity
    {
        $sleeperMatchup = new SleeperMatchupEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperMatchup->buildFindByCriteriaFromDto($leagueId, $week, $sleeperMatchupDto)
            ))) {
            $sleeperMatchup = $existingEntity;
        }

        return $sleeperMatchup;
    }

    /**
     * ATOMIC matchup write for one league: core upserts + starters/players
     * junction rebuild — one transaction, rollback + rethrow on failure. This is
     * the write phase previously embedded in SyncSleeperMatchupsBatchHandler.
     *
     * @param array<int, \HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup[]> $matchupsByWeek week => matchups[]
     */
    public function pgBulkUpsertMatchupsWithJunctions(string $leagueId, array $matchupsByWeek): void
    {
        $this->assertPostgres();
        if ($matchupsByWeek === []) {
            return;
        }

        $db = $this->db();
        $db->beginTransaction();
        try {
            $internalLeagueId = $this->sleeperLeagueRepository->pgFetchInternalId($leagueId);
            $rosterIdMap = $this->sleeperRosterRepository->pgFetchRosterIdMap($leagueId);

            $rows = [];
            foreach ($matchupsByWeek as $week => $weeklyMatchups) {
                foreach ($weeklyMatchups as $matchup) {
                    $rosterId = $matchup->getRosterId();
                    if (!$rosterId || !isset($rosterIdMap[$rosterId])) {
                        continue;
                    }

                    $rows[] = [
                        'internal_league_id' => $internalLeagueId,
                        'internal_roster_id' => $rosterIdMap[$rosterId],
                        'league_id' => $leagueId,
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
                $this->pgUpsertMatchupChunk($chunk);
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
                $matchupPkMapByWeek = $this->pgFetchMatchupPkMapByWeek($leagueId, $weekToRosterIds);
                [$allExtPids, $startersByMid, $playersByMid] = $this->collectPlayersForJunctions(
                    $matchupsByWeek, $matchupPkMapByWeek
                );

                $playerIdMap = $this->sleeperPlayerRepository->pgFetchPlayerIdMap($allExtPids);
                $matchupDbIds = array_keys($startersByMid + $playersByMid);

                if ($matchupDbIds !== []) {
                    $this->pgWipeJunctions($matchupDbIds);

                    $starterRows = [];
                    foreach ($startersByMid as $mid => $extPids) {
                        foreach ($extPids as $pid) {
                            if (isset($playerIdMap[$pid])) {
                                $starterRows[] = ['matchup_id' => (int)$mid, 'player_id' => $playerIdMap[$pid]];
                            }
                        }
                    }
                    foreach (array_chunk($starterRows, 1000) as $chunk) {
                        $this->pgInsertJunction('public.sasb_matchup_starters', $chunk);
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
                        $this->pgInsertJunction('public.sasb_matchup_players', $chunk);
                    }
                }
            }

            $db->commit();
        } catch (\Throwable $e) {
            if ($db->isTransactionActive()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Matchup PK lookup for playoff-matchup connections.
     * Matchup week = playoffWeekStart + round - 1
     *
     * @return array<string,int> ["rosterId:week" => internal matchup id]
     */
    public function pgFetchMatchupIdMapForPlayoffs(string $leagueId, array $rosterIds, int $playoffWeekStart): array
    {
        $this->assertPostgres();
        if ($rosterIds === []) {
            return [];
        }

        $rows = $this->db()->fetchAllAssociative(
            'SELECT id, roster_id, week FROM public.sasb_sleeper_matchup
             WHERE league_id = ? AND week >= ? AND roster_id IN (?)',
            [$leagueId, $playoffWeekStart, $rosterIds],
            [ParameterType::STRING, ParameterType::INTEGER, ArrayParameterType::INTEGER]
        );
        $map = [];
        foreach ($rows as $r) {
            $key = $r['roster_id'] . ':' . $r['week'];
            $map[$key] = (int)$r['id'];
        }
        return $map;
    }

    private function pgUpsertMatchupChunk(array $chunk): void
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

        $this->db()->executeStatement($sql, $params);
    }

    /** @return array<int,array<int,int>> [week => [roster_id => matchup_db_id]] */
    private function pgFetchMatchupPkMapByWeek(string $leagueId, array $weekToRosterIds): array
    {
        $out = [];
        foreach ($weekToRosterIds as $week => $rosterIds) {
            $rows = $this->db()->fetchAllAssociative(
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

    private function pgWipeJunctions(array $matchupDbIds): void
    {
        $ids = array_values(array_unique(array_map('intval', $matchupDbIds)));
        if ($ids === []) return;
        foreach (array_chunk($ids, 1000) as $chunk) {
            $this->db()->executeStatement('DELETE FROM public.sasb_matchup_starters WHERE matchup_id IN (?)', [$chunk], [ArrayParameterType::INTEGER]);
            $this->db()->executeStatement('DELETE FROM public.sasb_matchup_players WHERE matchup_id IN (?)', [$chunk], [ArrayParameterType::INTEGER]);
        }
    }

    private function pgInsertJunction(string $table, array $rows): void
    {
        if ($rows === []) return;
        $values = implode(',', array_fill(0, count($rows), '(?,?)'));
        $sql = "INSERT INTO {$table} (matchup_id, player_id) VALUES {$values} ON CONFLICT DO NOTHING";
        $params = [];
        foreach ($rows as $r) {
            $params[] = $r['matchup_id'];
            $params[] = $r['player_id'];
        }
        $this->db()->executeStatement($sql, $params);
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
