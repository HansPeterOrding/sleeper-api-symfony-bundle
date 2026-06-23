<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayoffMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayoffMatchupsBatchMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperPlayoffMatchupsBatchHandler
{
    public function __construct(
        private readonly Connection      $db,
        private readonly LoggerInterface $logger,
    )
    {
    }

    public function __invoke(SyncSleeperPlayoffMatchupsBatchMessage $message): void
    {
        if ($message->winnersData === [] && $message->losersData === []) {
            return;
        }

        $this->db->beginTransaction();
        try {
            $internalLeagueId = $this->fetchInternalLeagueId($message->leagueId);
            $playoffWeekStart = $this->fetchPlayoffWeekStart($message->leagueId);
            $rosterMap = $this->fetchRosterMap($message->leagueId);

            // Build all roster IDs appearing in brackets to pre-fetch matchup map
            $allRosterIds = $this->collectRosterIds($message->winnersData, $message->losersData);
            $matchupMap = $this->fetchMatchupMap($message->leagueId, $allRosterIds, $playoffWeekStart);

            $rows = [];
            foreach ($message->winnersData as $dto) {
                $rows[] = $this->buildRow($dto, AbstractEndpoint::BRANCH_WINNERS, $message->leagueId, $internalLeagueId, $rosterMap, $matchupMap, $playoffWeekStart);
            }
            foreach ($message->losersData as $dto) {
                $rows[] = $this->buildRow($dto, AbstractEndpoint::BRANCH_LOSERS, $message->leagueId, $internalLeagueId, $rosterMap, $matchupMap, $playoffWeekStart);
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                $this->bulkUpsertPlayoffMatchups($chunk);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->logger->warning('SyncSleeperPlayoffMatchupsBatchHandler transient error', [
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
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

    private function fetchPlayoffWeekStart(string $leagueId): int
    {
        $week = $this->db->fetchOne(
            'SELECT settings_playoff_week_start FROM public.sasb_sleeper_league WHERE league_id = ?',
            [$leagueId]
        );
        return (int)($week ?? 15);
    }

    /** @return array<int,int> [rosterId => internal id] */
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

    /**
     * Matchup week = playoffWeekStart + round - 1
     * @return array<string,int> ["rosterId:week" => internal matchup id]
     */
    private function fetchMatchupMap(string $leagueId, array $rosterIds, int $playoffWeekStart): array
    {
        if ($rosterIds === []) return [];

        $rows = $this->db->fetchAllAssociative(
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

    /** @return int[] */
    private function collectRosterIds(array $winners, array $losers): array
    {
        $ids = [];
        foreach (array_merge($winners, $losers) as $dto) {
            if ($dto->getT1() !== null) $ids[] = $dto->getT1();
            if ($dto->getT2() !== null) $ids[] = $dto->getT2();
        }
        return array_values(array_unique($ids));
    }

    private function buildRow(
        SleeperPlayoffMatchup $dto,
        string                $branch,
        string                $leagueId,
        int                   $internalLeagueId,
        array                 $rosterMap,
        array                 $matchupMap,
        int                   $playoffWeekStart,
    ): array
    {
        $t1 = $dto->getT1();
        $t2 = $dto->getT2();
        $r = $dto->getR();

        $matchupWeek = $r !== null ? $playoffWeekStart + $r - 1 : null;
        $matchupT1Id = ($t1 !== null && $matchupWeek !== null) ? ($matchupMap["{$t1}:{$matchupWeek}"] ?? null) : null;
        $matchupT2Id = ($t2 !== null && $matchupWeek !== null) ? ($matchupMap["{$t2}:{$matchupWeek}"] ?? null) : null;

        return [
            'league_id' => $leagueId,
            'branch' => $branch,
            'r' => $r,
            'm' => $dto->getM(),
            't1' => $t1,
            't2' => $t2,
            'w' => $dto->getW(),
            'l' => $dto->getL(),
            'p' => $dto->getP(),
            't1from_w' => $dto->getT1From()?->getW(),
            't1from_l' => $dto->getT1From()?->getL(),
            't2from_w' => $dto->getT2From()?->getW(),
            't2from_l' => $dto->getT2From()?->getL(),
            'internal_league_id' => $internalLeagueId,
            'internal_roster_id_t1' => $t1 !== null ? ($rosterMap[$t1] ?? null) : null,
            'internal_roster_id_t2' => $t2 !== null ? ($rosterMap[$t2] ?? null) : null,
            'internal_matchup_id_t1' => $matchupT1Id,
            'internal_matchup_id_t2' => $matchupT2Id,
        ];
    }

    private function bulkUpsertPlayoffMatchups(array $chunk): void
    {
        if ($chunk === []) return;

        $tuple = '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_playoff_matchup (
    league_id, branch, r, m, t1, t2, w, l, p,
    t1from_w, t1from_l, t2from_w, t2from_l,
    internal_league_id, internal_roster_id_t1, internal_roster_id_t2,
    internal_matchup_id_t1, internal_matchup_id_t2
) VALUES {$valuesSql}
ON CONFLICT (league_id, branch, m) DO UPDATE SET
    r                      = EXCLUDED.r,
    t1                     = EXCLUDED.t1,
    t2                     = EXCLUDED.t2,
    w                      = EXCLUDED.w,
    l                      = EXCLUDED.l,
    p                      = EXCLUDED.p,
    t1from_w               = EXCLUDED.t1from_w,
    t1from_l               = EXCLUDED.t1from_l,
    t2from_w               = EXCLUDED.t2from_w,
    t2from_l               = EXCLUDED.t2from_l,
    internal_roster_id_t1  = EXCLUDED.internal_roster_id_t1,
    internal_roster_id_t2  = EXCLUDED.internal_roster_id_t2,
    internal_matchup_id_t1 = EXCLUDED.internal_matchup_id_t1,
    internal_matchup_id_t2 = EXCLUDED.internal_matchup_id_t2
SQL;

        $params = [];
        foreach ($chunk as $r) {
            $params[] = $r['league_id'];
            $params[] = $r['branch'];
            $params[] = $r['r'];
            $params[] = $r['m'];
            $params[] = $r['t1'];
            $params[] = $r['t2'];
            $params[] = $r['w'];
            $params[] = $r['l'];
            $params[] = $r['p'];
            $params[] = $r['t1from_w'];
            $params[] = $r['t1from_l'];
            $params[] = $r['t2from_w'];
            $params[] = $r['t2from_l'];
            $params[] = $r['internal_league_id'];
            $params[] = $r['internal_roster_id_t1'];
            $params[] = $r['internal_roster_id_t2'];
            $params[] = $r['internal_matchup_id_t1'];
            $params[] = $r['internal_matchup_id_t2'];
        }

        $this->db->executeStatement($sql, $params);
    }
}
