<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTradedPick as SleeperTradedPickApiDto;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTradedPick as SleeperTradedPickDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTradedPick as SleeperTradedPickEntity;

/**
 * @method SleeperTradedPickEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperTradedPickEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperTradedPickEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperTradedPickEntity[] findAll()
 */
class SleeperTradedPickRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

    public function __construct(
        ManagerRegistry                          $registry,
        private readonly SleeperDraftRepository  $sleeperDraftRepository,
        private readonly SleeperLeagueRepository $sleeperLeagueRepository,
        private readonly SleeperRosterRepository $sleeperRosterRepository,
    )
    {
        parent::__construct($registry, SleeperTradedPickEntity::class);
    }

    public function findByDtoOrCreateEntity(string $leagueId, string $draftId, SleeperTradedPickDto $sleeperTradedPickDto): SleeperTradedPickEntity
    {
        $sleeperTradedPick = new SleeperTradedPickEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperTradedPick->buildFindByCriteriaFromDto($leagueId, $draftId, $sleeperTradedPickDto)
            ))) {
            $sleeperTradedPick = $existingEntity;
        }

        return $sleeperTradedPick;
    }

    /**
     * ATOMIC traded-pick write — one transaction, rollback + rethrow. Write
     * phase previously embedded in SyncSleeperTradedPicksBatchHandler.
     *
     * @param SleeperTradedPickApiDto[] $tradedPicks
     */
    public function pgBulkUpsertTradedPicks(string $draftId, string $leagueId, array $tradedPicks): void
    {
        $this->assertPostgres();
        if ($tradedPicks === []) {
            return;
        }

        $db = $this->db();
        $db->beginTransaction();
        try {
            $internalDraftId = $this->sleeperDraftRepository->pgFetchInternalId($draftId);
            $internalLeagueId = $this->sleeperLeagueRepository->pgFetchInternalId($leagueId);

            $allRosterIds = [];
            $allOwnerIds = [];
            $allPrevOwnerIds = [];

            foreach ($tradedPicks as $dto) {
                $allRosterIds[] = $dto->getRosterId();
                $allOwnerIds[] = $dto->getOwnerId();
                $allPrevOwnerIds[] = $dto->getPreviousOwnerId();
            }

            $rosterMap = $this->sleeperRosterRepository->pgFetchRosterIdMap($leagueId, array_unique($allRosterIds));
            $userMap = $this->sleeperRosterRepository->pgFetchOwnerUserIdMapByRosterId(
                $leagueId,
                array_unique(array_merge($allOwnerIds, $allPrevOwnerIds))
            );

            foreach (array_chunk($tradedPicks, 200) as $chunk) {
                $this->pgUpsertTradedPickChunk($chunk, $leagueId, $draftId, $internalDraftId, $internalLeagueId, $rosterMap, $userMap);
            }

            $db->commit();
        } catch (\Throwable $e) {
            if ($db->isTransactionActive()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /** @param SleeperTradedPickApiDto[] $chunk */
    private function pgUpsertTradedPickChunk(
        array  $chunk,
        string $leagueId,
        string $draftId,
        int    $internalDraftId,
        int    $internalLeagueId,
        array  $rosterMap,
        array  $userMap,
    ): void
    {
        if ($chunk === []) return;

        $tuple = '(?,?,?,?,?,?,?,?,?,?,?,?)';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_traded_pick (
    season, round, roster_id, previous_owner_id, owner_id,
    draft_id, league_id,
    internal_draft_id, internal_league_id,
    internal_roster_id, internal_previous_owner_id, internal_owner_id
) VALUES {$valuesSql}
ON CONFLICT (league_id, draft_id, season, round, roster_id) DO UPDATE SET
    previous_owner_id          = EXCLUDED.previous_owner_id,
    owner_id                   = EXCLUDED.owner_id,
    internal_roster_id         = EXCLUDED.internal_roster_id,
    internal_previous_owner_id = EXCLUDED.internal_previous_owner_id,
    internal_owner_id          = EXCLUDED.internal_owner_id
SQL;

        $params = [];
        foreach ($chunk as $dto) {
            $rosterId = $dto->getRosterId();
            $ownerId = $dto->getOwnerId();
            $prevOwnerId = $dto->getPreviousOwnerId();

            $params[] = $dto->getSeason();
            $params[] = $dto->getRound();
            $params[] = $rosterId;
            $params[] = $prevOwnerId;
            $params[] = $ownerId;
            // Use caller draftId as authoritative source — the league-level traded_picks
            // endpoint does not populate draft_id on the DTO (same situation as leagueId)
            $params[] = $draftId;
            // Use caller leagueId as authoritative source — DTO leagueId is not always populated
            $params[] = $leagueId;
            $params[] = $internalDraftId;
            $params[] = $internalLeagueId;
            $params[] = $rosterMap[$rosterId] ?? null;
            $params[] = $userMap[$prevOwnerId] ?? null;
            $params[] = $userMap[$ownerId] ?? null;
        }

        $this->db()->executeStatement($sql, $params);
    }

}
