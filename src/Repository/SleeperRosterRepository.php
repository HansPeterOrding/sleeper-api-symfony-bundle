<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use HansPeterOrding\SleeperApiClient\Dto\SleeperRoster as SleeperRosterApiDto;
use HansPeterOrding\SleeperApiClient\Dto\SleeperUser as SleeperUserApiDto;
use HansPeterOrding\SleeperApiClient\Dto\SleeperRoster as SleeperRosterDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster as SleeperRosterEntity;

/**
 * @method SleeperRosterEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperRosterEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperRosterEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperRosterEntity[] findAll()
 */
class SleeperRosterRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

    public function __construct(
        ManagerRegistry                          $registry,
        private readonly SleeperUserRepository   $sleeperUserRepository,
        private readonly SleeperLeagueRepository $sleeperLeagueRepository,
    )
    {
        parent::__construct($registry, SleeperRosterEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperRosterDto $sleeperRosterDto): SleeperRosterEntity
    {
        $sleeperRoster = new SleeperRosterEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperRoster->buildFindByCriteriaFromDto($sleeperRosterDto)
            ))) {
            $sleeperRoster = $existingEntity;
        }

        return $sleeperRoster;
    }

    /**
     * ATOMIC users+rosters write for one league: upsert users, resolve the
     * owner map, upsert rosters — one transaction, rollback + rethrow on any
     * failure. This is the write phase previously embedded in
     * SyncSleeperUsersAndRostersBatchHandler.
     *
     * @param SleeperUserApiDto[] $users
     * @param SleeperRosterApiDto[] $rosters
     */
    public function pgUpsertUsersAndRosters(string $leagueId, array $users, array $rosters): void
    {
        $this->assertPostgres();
        $db = $this->db();
        $db->beginTransaction();
        try {
            $internalLeagueId = $this->sleeperLeagueRepository->pgFetchInternalId($leagueId);

            $this->sleeperUserRepository->pgBulkUpsertUsers($users);

            $ownerIds = array_values(array_filter(array_unique(
                array_map(fn(SleeperRosterApiDto $r) => $r->getOwnerId(), $rosters)
            )));
            $userMap = $this->sleeperUserRepository->pgFetchUserIdMap($ownerIds);

            foreach (array_chunk($rosters, 200) as $chunk) {
                $this->pgUpsertRosterChunk($chunk, $internalLeagueId, $userMap);
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
     * @return array<int,int> [roster_id => internal id]; pass $rosterIds = null
     *                        for all rosters of the league
     */
    public function pgFetchRosterIdMap(string $leagueId, ?array $rosterIds = null): array
    {
        $this->assertPostgres();
        if ($rosterIds !== null) {
            $rosterIds = array_values(array_unique(array_filter($rosterIds, static fn($v) => $v !== null)));
            if ($rosterIds === []) {
                return [];
            }
            $rows = $this->db()->fetchAllAssociative(
                'SELECT roster_id, id FROM public.sasb_sleeper_roster WHERE league_id = ? AND roster_id IN (?)',
                [$leagueId, $rosterIds],
                [ParameterType::STRING, ArrayParameterType::INTEGER]
            );
        } else {
            $rows = $this->db()->fetchAllAssociative(
                'SELECT roster_id, id FROM public.sasb_sleeper_roster WHERE league_id = ?',
                [$leagueId]
            );
        }
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['roster_id']] = (int)$r['id'];
        }
        return $map;
    }

    /** @return array<int,int> [rosterId => internal user id of the roster owner] */
    public function pgFetchOwnerUserIdMapByRosterId(string $leagueId, array $rosterIds): array
    {
        $this->assertPostgres();
        $rosterIds = array_values(array_unique(array_filter($rosterIds, static fn($v) => $v !== null)));
        if ($rosterIds === []) {
            return [];
        }
        $rows = $this->db()->fetchAllAssociative(
            'SELECT r.roster_id, u.id as user_id
             FROM public.sasb_sleeper_roster r
             JOIN public.sasb_sleeper_user u ON u.id = r.internal_owner_id
             WHERE r.league_id = ? AND r.roster_id IN (?)',
            [$leagueId, $rosterIds],
            [ParameterType::STRING, ArrayParameterType::INTEGER]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['roster_id']] = (int)$r['user_id'];
        }
        return $map;
    }

    /** @param SleeperRosterApiDto[] $chunk */
    private function pgUpsertRosterChunk(array $chunk, int $internalLeagueId, array $userMap): void
    {
        if ($chunk === []) {
            return;
        }

        $tuple = '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_roster (
    roster_id, owner_id, league_id, internal_league_id, internal_owner_id,
    starters, reserve, keepers, taxi, players,
    rostersettings_wins, rostersettings_waiver_position, rostersettings_waiver_budget_used,
    rostersettings_total_moves, rostersettings_ties, rostersettings_losses,
    rostersettings_fpts_decimal, rostersettings_fpts_against_decimal,
    rostersettings_fpts_against, rostersettings_fpts, rostersettings_eliminated,
    co_owners,
    rostermetadata_player_nicknames, rostermetadata_record, rostermetadata_streak
) VALUES {$valuesSql}
ON CONFLICT (league_id, roster_id) DO UPDATE SET
    owner_id                            = EXCLUDED.owner_id,
    internal_owner_id                   = EXCLUDED.internal_owner_id,
    starters                            = EXCLUDED.starters,
    reserve                             = EXCLUDED.reserve,
    keepers                             = EXCLUDED.keepers,
    taxi                                = EXCLUDED.taxi,
    players                             = EXCLUDED.players,
    rostersettings_wins                 = EXCLUDED.rostersettings_wins,
    rostersettings_waiver_position      = EXCLUDED.rostersettings_waiver_position,
    rostersettings_waiver_budget_used   = EXCLUDED.rostersettings_waiver_budget_used,
    rostersettings_total_moves          = EXCLUDED.rostersettings_total_moves,
    rostersettings_ties                 = EXCLUDED.rostersettings_ties,
    rostersettings_losses               = EXCLUDED.rostersettings_losses,
    rostersettings_fpts_decimal         = EXCLUDED.rostersettings_fpts_decimal,
    rostersettings_fpts_against_decimal = EXCLUDED.rostersettings_fpts_against_decimal,
    rostersettings_fpts_against         = EXCLUDED.rostersettings_fpts_against,
    rostersettings_fpts                 = EXCLUDED.rostersettings_fpts,
    rostersettings_eliminated           = EXCLUDED.rostersettings_eliminated,
    co_owners                           = EXCLUDED.co_owners,
    rostermetadata_player_nicknames     = EXCLUDED.rostermetadata_player_nicknames,
    rostermetadata_record               = EXCLUDED.rostermetadata_record,
    rostermetadata_streak               = EXCLUDED.rostermetadata_streak
SQL;

        $params = [];
        foreach ($chunk as $dto) {
            $ownerId = $dto->getOwnerId();
            $internalOwnerId = $ownerId ? ($userMap[$ownerId] ?? null) : null;
            $settings = $dto->getSettings();
            $metadata = $dto->getMetadata();

            $params[] = $dto->getRosterId();
            $params[] = $ownerId;
            $params[] = $dto->getLeagueId();
            $params[] = $internalLeagueId;
            $params[] = $internalOwnerId;
            $params[] = json_encode($dto->getStarters());
            $params[] = json_encode($dto->getReserve());
            $params[] = json_encode($dto->getKeepers());
            $params[] = json_encode($dto->getTaxi());
            $params[] = json_encode($dto->getPlayers());
            $params[] = $settings?->getWins() ?? 0;
            $params[] = $settings?->getWaiverPosition() ?? 0;
            $params[] = $settings?->getWaiverBudgetUsed() ?? 0;
            $params[] = $settings?->getTotalMoves() ?? 0;
            $params[] = $settings?->getTies() ?? 0;
            $params[] = $settings?->getLosses() ?? 0;
            $params[] = $settings?->getFptsDecimal() ?? 0;
            $params[] = $settings?->getFptsAgainstDecimal() ?? 0;
            $params[] = $settings?->getFptsAgainst() ?? 0;
            $params[] = $settings?->getFpts() ?? 0;
            $params[] = $settings?->getEliminated();
            $params[] = json_encode($dto->getCoOwners());
            $params[] = json_encode($metadata?->getPlayerNicknames() ?? []);
            $params[] = $metadata?->getRecord();
            $params[] = $metadata?->getStreak();
        }

        $this->db()->executeStatement($sql, $params);
    }

}
