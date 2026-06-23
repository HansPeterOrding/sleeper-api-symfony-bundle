<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTransaction as SleeperTransactionDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTransactionsBatchMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperTransactionsBatchHandler
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function __invoke(SyncSleeperTransactionsBatchMessage $message): void
    {
        if ($message->transactions === []) {
            return;
        }

        $this->db->beginTransaction();
        try {
            $internalLeagueId = $this->fetchInternalLeagueId($message->leagueId);

            // 1) Collect all external IDs needed for related entities
            $allCreatorIds = [];
            $allRosterIds = [];
            $allPlayerIds = [];

            foreach ($message->transactions as $dto) {
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
            $userMap = $this->fetchUserMap(array_keys($allCreatorIds));
            $rosterMap = $this->fetchRosterMap($message->leagueId, array_keys($allRosterIds));
            $playerMap = $this->fetchPlayerMap(array_keys($allPlayerIds));

            // 3) Bulk upsert main transaction rows
            $rows = [];
            foreach ($message->transactions as $dto) {
                $rows[] = $this->buildRow($dto, $internalLeagueId, $userMap);
            }

            foreach (array_chunk($rows, 200) as $chunk) {
                $this->bulkUpsertTransactions($chunk);
            }

            // 4) Fetch internal transaction IDs for junction handling
            $transactionIds = array_column($rows, 'transaction_id');
            $txIdMap = $this->fetchTransactionIdMap($transactionIds);

            if ($txIdMap === []) {
                $this->db->commit();
                return;
            }

            // 5) Wipe junction tables for affected transactions
            $internalTxIds = array_values($txIdMap);
            $this->wipeJunctions($internalTxIds);

            // 6) Build and insert junction rows
            $rosterJunctionRows = [];
            $droppedJunctionRows = [];
            $addedJunctionRows = [];
            $consenterJunctionRows = [];

            foreach ($message->transactions as $dto) {
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
                $this->bulkInsertJunction('public.sasb_sleeper_transaction_sleeper_roster', ['transaction_id', 'roster_id'], $chunk);
            }
            foreach (array_chunk($droppedJunctionRows, 1000) as $chunk) {
                $this->bulkInsertJunction('public.sasb_sleeper_transactions_dropped_players', ['transaction_id', 'player_id'], $chunk);
            }
            foreach (array_chunk($addedJunctionRows, 1000) as $chunk) {
                $this->bulkInsertJunction('public.sasb_sleeper_transactions_added_players', ['transaction_id', 'player_id'], $chunk);
            }
            foreach (array_chunk($consenterJunctionRows, 1000) as $chunk) {
                $this->bulkInsertJunction('public.sasb_sleeper_transactions_consenter_users', ['transaction_id', 'roster_id'], $chunk);
            }

            $this->db->commit();

        } catch (\Throwable $e) {
            $this->db->rollBack();
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
            throw new \RuntimeException("League {$leagueId} not found (run league importer first).");
        }
        return (int)$id;
    }

    /** @return array<string,int> [userId => internal id] */
    private function fetchUserMap(array $userIds): array
    {
        if ($userIds === []) {
            return [];
        }
        $rows = $this->db->fetchAllAssociative(
            'SELECT user_id, id FROM public.sasb_sleeper_user WHERE user_id IN (?)',
            [$userIds],
            [ArrayParameterType::STRING]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[$r['user_id']] = (int)$r['id'];
        }
        return $map;
    }

    /** @return array<int,int> [rosterId => internal id] */
    private function fetchRosterMap(string $leagueId, array $rosterIds): array
    {
        if ($rosterIds === []) {
            return [];
        }
        $rows = $this->db->fetchAllAssociative(
            'SELECT roster_id, id FROM public.sasb_sleeper_roster WHERE league_id = ? AND roster_id IN (?)',
            [$leagueId, $rosterIds],
            [ParameterType::STRING, ArrayParameterType::INTEGER]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['roster_id']] = (int)$r['id'];
        }
        return $map;
    }

    /** @return array<string,int> [playerId => internal id] */
    private function fetchPlayerMap(array $playerIds): array
    {
        if ($playerIds === []) {
            return [];
        }
        $map = [];
        foreach (array_chunk($playerIds, 1000) as $chunk) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT player_id, id FROM public.sasb_sleeper_player WHERE player_id IN (?)',
                [$chunk],
                [ArrayParameterType::STRING]
            );
            foreach ($rows as $r) {
                $map[$r['player_id']] = (int)$r['id'];
            }
        }
        return $map;
    }

    /** @return array<string,int> [transactionId => internal id] */
    private function fetchTransactionIdMap(array $transactionIds): array
    {
        if ($transactionIds === []) {
            return [];
        }
        $rows = $this->db->fetchAllAssociative(
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

    private function buildRow(SleeperTransactionDto $dto, int $internalLeagueId, array $userMap): array
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
    private function bulkUpsertTransactions(array $chunk): void
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

        $this->db->executeStatement($sql, $params);
    }

    private function wipeJunctions(array $internalTxIds): void
    {
        if ($internalTxIds === []) {
            return;
        }
        foreach (array_chunk($internalTxIds, 1000) as $chunk) {
            $this->db->executeStatement(
                'DELETE FROM public.sasb_sleeper_transaction_sleeper_roster WHERE transaction_id IN (?)',
                [$chunk], [ArrayParameterType::INTEGER]
            );
            $this->db->executeStatement(
                'DELETE FROM public.sasb_sleeper_transactions_dropped_players WHERE transaction_id IN (?)',
                [$chunk], [ArrayParameterType::INTEGER]
            );
            $this->db->executeStatement(
                'DELETE FROM public.sasb_sleeper_transactions_added_players WHERE transaction_id IN (?)',
                [$chunk], [ArrayParameterType::INTEGER]
            );
            $this->db->executeStatement(
                'DELETE FROM public.sasb_sleeper_transactions_consenter_users WHERE transaction_id IN (?)',
                [$chunk], [ArrayParameterType::INTEGER]
            );
        }
    }

    /**
     * @param string[] $columns exactly two column names
     * @param array<int,array<string,int>> $rows
     */
    private function bulkInsertJunction(string $table, array $columns, array $rows): void
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

        $this->db->executeStatement($sql, $params);
    }
}
