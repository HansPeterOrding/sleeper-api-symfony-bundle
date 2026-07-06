<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftPick as SleeperDraftPickApiDto;
use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftPick as SleeperDraftPickDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick as SleeperDraftPickEntity;

/**
 * @method SleeperDraftPickEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperDraftPickEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperDraftPickEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperDraftPickEntity[] findAll()
 */
class SleeperDraftPickRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

    public function __construct(
        ManagerRegistry                          $registry,
        private readonly SleeperDraftRepository  $sleeperDraftRepository,
        private readonly SleeperRosterRepository $sleeperRosterRepository,
        private readonly SleeperUserRepository   $sleeperUserRepository,
        private readonly SleeperPlayerRepository $sleeperPlayerRepository,
    )
    {
        parent::__construct($registry, SleeperDraftPickEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperDraftPickDto $sleeperDraftPickDto): SleeperDraftPickEntity
    {
        $sleeperDraftPick = new SleeperDraftPickEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperDraftPick->buildFindByCriteriaFromDto($sleeperDraftPickDto)
            ))) {
            $sleeperDraftPick = $existingEntity;
        }

        return $sleeperDraftPick;
    }

    /**
     * ATOMIC draft-pick write: resolves the draft/player/user/roster id maps and
     * bulk-upserts the picks — one transaction, rollback + rethrow. Write phase
     * previously embedded in SyncSleeperDraftPicksBatchHandler.
     *
     * @param SleeperDraftPickApiDto[] $picks
     */
    public function pgBulkUpsertDraftPicks(string $draftId, array $picks): void
    {
        $this->assertPostgres();
        if ($picks === []) {
            return;
        }

        $db = $this->db();
        $db->beginTransaction();
        try {
            $internalDraftId = $this->sleeperDraftRepository->pgFetchInternalId($draftId);

            $playerIds = array_values(array_filter(array_unique(
                array_map(fn(SleeperDraftPickApiDto $p) => $p->getPlayerId(), $picks)
            )));
            $pickedByIds = array_values(array_filter(array_unique(
                array_map(fn(SleeperDraftPickApiDto $p) => $p->getPickedBy(), $picks)
            )));
            $rosterIds = array_values(array_filter(array_unique(
                array_map(fn(SleeperDraftPickApiDto $p) => $p->getRosterId(), $picks)
            )));

            $playerMap = $this->sleeperPlayerRepository->pgFetchPlayerIdMap($playerIds);
            $userMap = $this->sleeperUserRepository->pgFetchUserIdMap($pickedByIds);

            $leagueId = $this->sleeperDraftRepository->pgFetchLeagueId($draftId);
            $rosterMap = $leagueId !== null
                ? $this->sleeperRosterRepository->pgFetchRosterIdMap($leagueId, $rosterIds)
                : [];

            foreach (array_chunk($picks, 200) as $chunk) {
                $this->pgUpsertDraftPickChunk($chunk, $internalDraftId, $playerMap, $userMap, $rosterMap);
            }

            $db->commit();
        } catch (\Throwable $e) {
            if ($db->isTransactionActive()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /** @param SleeperDraftPickApiDto[] $chunk */
    private function pgUpsertDraftPickChunk(
        array $chunk,
        int   $internalDraftId,
        array $playerMap,
        array $userMap,
        array $rosterMap,
    ): void
    {
        if ($chunk === []) return;

        $tuple = '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_draft_pick (
    draft_id, pick_no, round, draft_slot, roster_id, player_id, picked_by, is_keeper,
    internal_draft_id, internal_player_id, internal_roster_id, internal_user_id,
    metadata_first_name, metadata_last_name, metadata_player_id, metadata_position,
    metadata_team, metadata_years_exp, metadata_sport, metadata_status,
    metadata_number, metadata_news_updated, metadata_injury_status, metadata_amount
) VALUES {$valuesSql}
ON CONFLICT (draft_id, pick_no) DO UPDATE SET
    round                  = EXCLUDED.round,
    draft_slot             = EXCLUDED.draft_slot,
    roster_id              = EXCLUDED.roster_id,
    player_id              = EXCLUDED.player_id,
    picked_by              = EXCLUDED.picked_by,
    is_keeper              = EXCLUDED.is_keeper,
    internal_player_id     = EXCLUDED.internal_player_id,
    internal_roster_id     = EXCLUDED.internal_roster_id,
    internal_user_id       = EXCLUDED.internal_user_id,
    metadata_first_name    = EXCLUDED.metadata_first_name,
    metadata_last_name     = EXCLUDED.metadata_last_name,
    metadata_player_id     = EXCLUDED.metadata_player_id,
    metadata_position      = EXCLUDED.metadata_position,
    metadata_team          = EXCLUDED.metadata_team,
    metadata_years_exp     = EXCLUDED.metadata_years_exp,
    metadata_sport         = EXCLUDED.metadata_sport,
    metadata_status        = EXCLUDED.metadata_status,
    metadata_number        = EXCLUDED.metadata_number,
    metadata_news_updated  = EXCLUDED.metadata_news_updated,
    metadata_injury_status = EXCLUDED.metadata_injury_status,
    metadata_amount        = EXCLUDED.metadata_amount
SQL;

        $params = [];
        foreach ($chunk as $dto) {
            $playerId = $dto->getPlayerId();
            $pickedBy = $dto->getPickedBy();
            $rosterId = $dto->getRosterId();
            $metadata = $dto->getMetadata();

            $params[] = $dto->getDraftId();
            $params[] = $dto->getPickNo();
            $params[] = $dto->getRound();
            $params[] = $dto->getDraftSlot();
            $params[] = $rosterId;
            $params[] = $playerId;
            $params[] = $pickedBy;
            $params[] = $dto->getIsKeeper() ? 'true' : 'false';
            $params[] = $internalDraftId;
            $params[] = $playerId ? ($playerMap[$playerId] ?? null) : null;
            $params[] = $rosterId ? ($rosterMap[$rosterId] ?? null) : null;
            $params[] = $pickedBy ? ($userMap[$pickedBy] ?? null) : null;
            // All metadata fields are plain strings on the DTO — no enum conversion needed
            $params[] = $metadata?->getFirstName();
            $params[] = $metadata?->getLastName();
            $params[] = $metadata?->getPlayerId();
            $params[] = $metadata?->getPosition();
            $params[] = $metadata?->getTeam();
            $params[] = $metadata?->getYearsExp();
            $params[] = $metadata?->getSport();
            $params[] = ($metadata?->getStatus() ?: null);
            $params[] = $metadata?->getNumber();
            $params[] = $metadata?->getNewsUpdated();
            $params[] = ($metadata?->getInjuryStatus() ?: null);
            $params[] = $metadata?->getAmount();
        }

        $this->db()->executeStatement($sql, $params);
    }

}
