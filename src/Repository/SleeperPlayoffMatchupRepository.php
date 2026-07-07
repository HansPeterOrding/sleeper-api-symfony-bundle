<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayoffMatchup as SleeperPlayoffMatchupApiDto;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayoffMatchup as SleeperPlayoffMatchupDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup as SleeperPlayoffMatchupEntity;

/**
 * @method SleeperPlayoffMatchup|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayoffMatchup|null  findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayoffMatchup[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayoffMatchup[] findAll()
 */
class SleeperPlayoffMatchupRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

    public function __construct(
        ManagerRegistry                           $registry,
        private readonly SleeperLeagueRepository  $sleeperLeagueRepository,
        private readonly SleeperRosterRepository  $sleeperRosterRepository,
        private readonly SleeperMatchupRepository $sleeperMatchupRepository,
    )
    {
        parent::__construct($registry, SleeperPlayoffMatchupEntity::class);
    }

    public function findByDtoOrCreateEntity(string $leagueId, string $branch, SleeperPlayoffMatchupDto $sleeperPlayoffMatchupDto): SleeperPlayoffMatchupEntity
    {
        $sleeperPlayoffMatchup = new SleeperPlayoffMatchupEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayoffMatchup->buildFindByCriteriaFromDto($leagueId, $branch, $sleeperPlayoffMatchupDto)
            ))) {
            $sleeperPlayoffMatchup = $existingEntity;
        }

        return $sleeperPlayoffMatchup;
    }

    /**
     * ATOMIC playoff-bracket write (both branches) — one transaction, rollback +
     * rethrow. Write phase previously embedded in
     * SyncSleeperPlayoffMatchupsBatchHandler.
     *
     * @param SleeperPlayoffMatchupApiDto[] $winnersData
     * @param SleeperPlayoffMatchupApiDto[] $losersData
     */
    public function pgBulkUpsertPlayoffMatchups(string $leagueId, array $winnersData, array $losersData): void
    {
        $this->assertPostgres();
        if ($winnersData === [] && $losersData === []) {
            return;
        }

        $db = $this->db();
        $db->beginTransaction();
        try {
            $internalLeagueId = $this->sleeperLeagueRepository->pgFetchInternalId($leagueId);
            $playoffWeekStart = $this->sleeperLeagueRepository->pgFetchPlayoffWeekStart($leagueId);
            $rosterMap = $this->sleeperRosterRepository->pgFetchRosterIdMap($leagueId);

            $allRosterIds = $this->collectRosterIds($winnersData, $losersData);
            $matchupMap = $this->sleeperMatchupRepository->pgFetchMatchupIdMapForPlayoffs($leagueId, $allRosterIds, $playoffWeekStart);

            $rows = [];
            foreach ($winnersData as $dto) {
                $rows[] = $this->buildRow($dto, AbstractEndpoint::BRANCH_WINNERS, $leagueId, $internalLeagueId, $rosterMap, $matchupMap, $playoffWeekStart);
            }
            foreach ($losersData as $dto) {
                $rows[] = $this->buildRow($dto, AbstractEndpoint::BRANCH_LOSERS, $leagueId, $internalLeagueId, $rosterMap, $matchupMap, $playoffWeekStart);
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                $this->pgUpsertPlayoffMatchupChunk($chunk);
            }

            $db->commit();
        } catch (\Throwable $e) {
            if ($db->isTransactionActive()) {
                $db->rollBack();
            }
            throw $e;
        }
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
        SleeperPlayoffMatchupApiDto $dto,
        string                      $branch,
        string                      $leagueId,
        int                         $internalLeagueId,
        array                       $rosterMap,
        array                       $matchupMap,
        int                         $playoffWeekStart,
    ): array
    {
        $t1 = $dto->getT1();
        $t2 = $dto->getT2();
        $r = $dto->getR();

        $matchupWeek = ($r !== null && $playoffWeekStart >= 1) ? $playoffWeekStart + $r - 1 : null;
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

    private function pgUpsertPlayoffMatchupChunk(array $chunk): void
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

        $this->db()->executeStatement($sql, $params);
    }

}
