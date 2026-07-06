<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\ArrayParameterType;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTransaction as SleeperTransactionApiDto;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTransaction as SleeperTransactionDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransaction;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransaction as SleeperTransactionEntity;

/**
 * @method SleeperTransactionEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperTransactionEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperTransactionEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperTransactionEntity[] findAll()
 */
class SleeperTransactionRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

    public function __construct(
        ManagerRegistry                          $registry,
        private readonly SleeperLeagueRepository $sleeperLeagueRepository,
        private readonly SleeperRosterRepository $sleeperRosterRepository,
        private readonly SleeperUserRepository   $sleeperUserRepository,
        private readonly SleeperPlayerRepository $sleeperPlayerRepository,
    )
    {
        parent::__construct($registry, SleeperTransactionEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperTransactionDto $sleeperTransactionDto): SleeperTransactionEntity
    {
        $sleeperTransaction = new SleeperTransaction();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperTransaction->buildFindByCriteriaFromDto($sleeperTransactionDto)
            ))) {
            $sleeperTransaction = $existingEntity;
        }

        return $sleeperTransaction;
    }

    /**
     * ATOMIC transaction write for one league: core upserts + all four junction
     * tables (rosters, dropped players, added players, consenters) — one
     * transaction, rollback + rethrow. This is the write phase previously
     * embedded in SyncSleeperTransactionsBatchHandler.
     *
     * @param SleeperTransactionApiDto[] $transactions
     */
    public function pgBulkUpsertTransactionsWithJunctions(string $leagueId, array $transactions): void
    {
        $this->assertPostgres();
        if ($transactions === []) {
            return;
        }

        $db = $this->db();
        $db->beginTransaction();
        try {
            $internalLeagueId = $this->sleeperLeagueRepository->pgFetchInternalId($leagueId);

            // 1) Collect all external IDs needed for related entities
            $allCreatorIds = [];
            $allRosterIds = [];
            $allPlayerIds = [];

            foreach ($transactions as $dto) {
                if ($dto->getCreator()) {
                    $allCreatorIds[$dto->getCreator()] = true;
                }
                foreach ($dto->getRosterIds() ?? [] as $rid) {
                    $allRosterIds[$rid] = true;
                }
                foreach ($dto->getConsenterIds() ?? [] as $rid) {
                    $allRosterIds[$rid] = true;
                }
                foreach (array_keys($dto->getDrops() ?? []) as $pid) {
                    $allPlayerIds[$pid] = true;
                }
                foreach (array_keys($dto->getAdds() ?? []) as $pid) {
                    $allPlayerIds[$pid] = true;
                }
            }

            // 2) Bulk-fetch all maps in one query each
            $userMap = $this->sleeperUserRepository->pgFetchUserIdMap(array_keys($allCreatorIds));
            $rosterMap = $this->sleeperRosterRepository->pgFetchRosterIdMap($leagueId, array_keys($allRosterIds));
            $playerMap = $this->sleeperPlayerRepository->pgFetchPlayerIdMap(array_keys($allPlayerIds));

            // 3) Bulk upsert main transaction rows
            $rows = [];
            foreach ($transactions as $dto) {
                $rows[] = $this->buildRow($dto, $internalLeagueId, $userMap);
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                $this->pgUpsertTransactionChunk($chunk);
            }

            // 4) Fetch internal transaction IDs for junction handling
            $transactionIds = array_column($rows, 'transaction_id');
            $txIdMap = $this->pgFetchTransactionIdMap($transactionIds);

            if ($txIdMap === []) {
                $db->commit();
                return;
            }

            // 5) Wipe junction tables for affected transactions
            $internalTxIds = array_values($txIdMap);
            $this->pgWipeJunctions($internalTxIds);

            // 6) Build and insert junction rows
            $rosterJunctionRows = [];
            $droppedJunctionRows = [];
            $addedJunctionRows = [];
            $consenterJunctionRows = [];

            foreach ($transactions as $dto) {
                $internalTxId = $txIdMap[$dto->getTransactionId()] ?? null;
                if ($internalTxId === null) {
                    continue;
                }

                foreach ($dto->getRosterIds() ?? [] as $rosterId) {
                    $internalRosterId = $rosterMap[$rosterId] ?? null;
                    if ($internalRosterId !== null) {
                        $rosterJunctionRows[] = ['transaction_id' => $internalTxId, 'roster_id' => $internalRosterId];
                    }
                }

                foreach (array_keys($dto->getDrops() ?? []) as $playerId) {
                    $internalPlayerId = $playerMap[$playerId] ?? null;
                    if ($internalPlayerId !== null) {
                        $droppedJunctionRows[] = ['transaction_id' => $internalTxId, 'player_id' => $internalPlayerId];
                    }
                }

                foreach (array_keys($dto->getAdds() ?? []) as $playerId) {
                    $internalPlayerId = $playerMap[$playerId] ?? null;
                    if ($internalPlayerId !== null) {
                        $addedJunctionRows[] = ['transaction_id' => $internalTxId, 'player_id' => $internalPlayerId];
                    }
                }

                foreach ($dto->getConsenterIds() ?? [] as $rosterId) {
                    $internalRosterId = $rosterMap[$rosterId] ?? null;
                    if ($internalRosterId !== null) {
                        $consenterJunctionRows[] = ['transaction_id' => $internalTxId, 'roster_id' => $internalRosterId];
                    }
                }
            }

            foreach (array_chunk($rosterJunctionRows, 1000) as $chunk) {
                $this->pgInsertJunction('public.sasb_sleeper_transaction_sleeper_roster', ['transaction_id', 'roster_id'], $chunk);
            }
            foreach (array_chunk($droppedJunctionRows, 1000) as $chunk) {
                $this->pgInsertJunction('public.sasb_sleeper_transactions_dropped_players', ['transaction_id', 'player_id'], $chunk);
            }
            foreach (array_chunk($addedJunctionRows, 1000) as $chunk) {
                $this->pgInsertJunction('public.sasb_sleeper_transactions_added_players', ['transaction_id', 'player_id'], $chunk);
            }
            foreach (array_chunk($consenterJunctionRows, 1000) as $chunk) {
                $this->pgInsertJunction('public.sasb_sleeper_transactions_consenter_users', ['transaction_id', 'roster_id'], $chunk);
            }

            $db->commit();
        } catch (\Throwable $e) {
            if ($db->isTransactionActive()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /** @return array<string,int> [transactionId => internal id] */
    private function pgFetchTransactionIdMap(array $transactionIds): array
    {
        if ($transactionIds === []) {
            return [];
        }
        $rows = $this->db()->fetchAllAssociative(
            'SELECT transaction_id, id FROM public.sasb_sleeper_transaction WHERE transaction_id IN (?)',
            [$transactionIds],
            [ArrayParameterType::STRING]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[$r['transaction_id']] = (int)$r['id'];
        }
        return $map;
    }

    private function buildRow(SleeperTransactionApiDto $dto, int $internalLeagueId, array $userMap): array
    {
        $waiverBudget = $dto->getWaiverBudget();
        $settings = $dto->getSettings();
        $metadata = $dto->getMetadata();
        $creatorId = $dto->getCreator();

        return [
            'internal_league_id' => $internalLeagueId,
            'internal_sleeper_user_id' => $creatorId ? ($userMap[$creatorId] ?? null) : null,
            'transaction_id' => $dto->getTransactionId(),
            'type' => $dto->getType(),
            'status' => $dto->getStatus(),
            'status_updated' => $dto->getStatusUpdated(),
            'created' => $dto->getCreated(),
            'creator' => $creatorId,
            'leg' => $dto->getLeg(),
            'roster_ids' => json_encode($dto->getRosterIds()),
            'drops' => json_encode($dto->getDrops()),
            'adds' => json_encode($dto->getAdds()),
            'consenter_ids' => json_encode($dto->getConsenterIds()),
            'draft_picks' => json_encode($dto->getDraftPicks()),
            'waiver_budget_sender' => $waiverBudget?->getSender(),
            'waiver_budget_receiver' => $waiverBudget?->getReceiver(),
            'waiver_budget_amount' => $waiverBudget?->getAmount(),
            'settings_waiver_bid' => $settings?->getWaiverBid(),
            'settings_seq' => $settings?->getSeq(),
            'settings_priority' => $settings?->getPriority(),
            'metadata_notes' => $metadata?->getNotes(),
        ];
    }

    /** @param array<int,array<string,mixed>> $chunk */
    private function pgUpsertTransactionChunk(array $chunk): void
    {
        if ($chunk === []) {
            return;
        }

        $tuple = '(' . implode(',', [
                '?', '?', '?', '?', '?', '?', '?', '?', '?',
                '?::json', '?::json', '?::json', '?::json', '?::json',
                '?', '?', '?', '?', '?', '?', '?',
            ]) . ')';

        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_transaction (
    internal_league_id, internal_sleeper_user_id, transaction_id,
    type, status, status_updated, created, creator, leg,
    roster_ids, drops, adds, consenter_ids, draft_picks,
    waiver_budget_sender, waiver_budget_receiver, waiver_budget_amount,
    settings_waiver_bid, settings_seq, settings_priority,
    metadata_notes
) VALUES {$valuesSql}
ON CONFLICT (transaction_id) DO UPDATE SET
    internal_league_id       = EXCLUDED.internal_league_id,
    internal_sleeper_user_id = EXCLUDED.internal_sleeper_user_id,
    type                     = EXCLUDED.type,
    status                   = EXCLUDED.status,
    status_updated           = EXCLUDED.status_updated,
    created                  = EXCLUDED.created,
    creator                  = EXCLUDED.creator,
    leg                      = EXCLUDED.leg,
    roster_ids               = EXCLUDED.roster_ids,
    drops                    = EXCLUDED.drops,
    adds                     = EXCLUDED.adds,
    consenter_ids            = EXCLUDED.consenter_ids,
    draft_picks              = EXCLUDED.draft_picks,
    waiver_budget_sender     = EXCLUDED.waiver_budget_sender,
    waiver_budget_receiver   = EXCLUDED.waiver_budget_receiver,
    waiver_budget_amount     = EXCLUDED.waiver_budget_amount,
    settings_waiver_bid      = EXCLUDED.settings_waiver_bid,
    settings_seq             = EXCLUDED.settings_seq,
    settings_priority        = EXCLUDED.settings_priority,
    metadata_notes           = EXCLUDED.metadata_notes
SQL;

        $params = [];
        foreach ($chunk as $r) {
            $params[] = $r['internal_league_id'];
            $params[] = $r['internal_sleeper_user_id'];
            $params[] = $r['transaction_id'];
            $params[] = $r['type'];
            $params[] = $r['status'];
            $params[] = $r['status_updated'];
            $params[] = $r['created'];
            $params[] = $r['creator'];
            $params[] = $r['leg'];
            $params[] = $r['roster_ids'];
            $params[] = $r['drops'];
            $params[] = $r['adds'];
            $params[] = $r['consenter_ids'];
            $params[] = $r['draft_picks'];
            $params[] = $r['waiver_budget_sender'];
            $params[] = $r['waiver_budget_receiver'];
            $params[] = $r['waiver_budget_amount'];
            $params[] = $r['settings_waiver_bid'];
            $params[] = $r['settings_seq'];
            $params[] = $r['settings_priority'];
            $params[] = $r['metadata_notes'];
        }

        $this->db()->executeStatement($sql, $params);
    }

    private function pgWipeJunctions(array $internalTxIds): void
    {
        if ($internalTxIds === []) {
            return;
        }
        foreach (array_chunk($internalTxIds, 1000) as $chunk) {
            $this->db()->executeStatement(
                'DELETE FROM public.sasb_sleeper_transaction_sleeper_roster WHERE transaction_id IN (?)',
                [$chunk], [ArrayParameterType::INTEGER]
            );
            $this->db()->executeStatement(
                'DELETE FROM public.sasb_sleeper_transactions_dropped_players WHERE transaction_id IN (?)',
                [$chunk], [ArrayParameterType::INTEGER]
            );
            $this->db()->executeStatement(
                'DELETE FROM public.sasb_sleeper_transactions_added_players WHERE transaction_id IN (?)',
                [$chunk], [ArrayParameterType::INTEGER]
            );
            $this->db()->executeStatement(
                'DELETE FROM public.sasb_sleeper_transactions_consenter_users WHERE transaction_id IN (?)',
                [$chunk], [ArrayParameterType::INTEGER]
            );
        }
    }

    /**
     * @param string[] $columns exactly two column names
     * @param array<int,array<string,int>> $rows
     */
    private function pgInsertJunction(string $table, array $columns, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $values = implode(',', array_fill(0, count($rows), '(?,?)'));
        $sql = "INSERT INTO {$table} ({$columns[0]}, {$columns[1]}) VALUES {$values} ON CONFLICT DO NOTHING";

        $params = [];
        foreach ($rows as $r) {
            $params[] = $r[$columns[0]];
            $params[] = $r[$columns[1]];
        }

        $this->db()->executeStatement($sql, $params);
    }

}
