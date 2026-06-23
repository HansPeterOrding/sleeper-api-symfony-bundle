<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperDraftPicksBatchMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperDraftPicksBatchHandler
{
    public function __construct(
        private readonly Connection      $db,
        private readonly LoggerInterface $logger,
    )
    {
    }

    public function __invoke(SyncSleeperDraftPicksBatchMessage $message): void
    {
        if ($message->picks === []) {
            return;
        }

        $this->db->beginTransaction();
        try {
            $internalDraftId = $this->fetchInternalDraftId($message->draftId);

            $playerIds = array_values(array_filter(array_unique(
                array_map(fn(SleeperDraftPick $p) => $p->getPlayerId(), $message->picks)
            )));
            $pickedByIds = array_values(array_filter(array_unique(
                array_map(fn(SleeperDraftPick $p) => $p->getPickedBy(), $message->picks)
            )));
            $rosterIds = array_values(array_filter(array_unique(
                array_map(fn(SleeperDraftPick $p) => $p->getRosterId(), $message->picks)
            )));

            $playerMap = $this->fetchPlayerMap($playerIds);
            $userMap = $this->fetchUserMap($pickedByIds);
            $rosterMap = $this->fetchRosterMap($message->draftId, $rosterIds);

            foreach (array_chunk($message->picks, 200) as $chunk) {
                $this->bulkUpsertDraftPicks($chunk, $internalDraftId, $playerMap, $userMap, $rosterMap);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->logger->warning('SyncSleeperDraftPicksBatchHandler transient error', [
                'draftId' => $message->draftId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function fetchInternalDraftId(string $draftId): int
    {
        $id = $this->db->fetchOne(
            'SELECT id FROM public.sasb_sleeper_draft WHERE draft_id = ?',
            [$draftId]
        );
        if (!$id) {
            throw new \RuntimeException("Draft {$draftId} not found. Run draft sync first.");
        }
        return (int)$id;
    }

    /** @return array<string,int> [playerId => internal id] */
    private function fetchPlayerMap(array $playerIds): array
    {
        if ($playerIds === []) return [];
        $map = [];
        foreach (array_chunk($playerIds, 1000) as $chunk) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT player_id, id FROM public.sasb_sleeper_player WHERE player_id IN (?)',
                [$chunk], [ArrayParameterType::STRING]
            );
            foreach ($rows as $r) {
                $map[$r['player_id']] = (int)$r['id'];
            }
        }
        return $map;
    }

    /** @return array<string,int> [userId => internal id] */
    private function fetchUserMap(array $userIds): array
    {
        if ($userIds === []) return [];
        $rows = $this->db->fetchAllAssociative(
            'SELECT user_id, id FROM public.sasb_sleeper_user WHERE user_id IN (?)',
            [$userIds], [ArrayParameterType::STRING]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[$r['user_id']] = (int)$r['id'];
        }
        return $map;
    }

    /** @return array<int,int> [rosterId => internal id] */
    private function fetchRosterMap(string $draftId, array $rosterIds): array
    {
        if ($rosterIds === []) return [];

        $leagueId = $this->db->fetchOne(
            'SELECT league_id FROM public.sasb_sleeper_draft WHERE draft_id = ?',
            [$draftId]
        );
        if (!$leagueId) return [];

        $rows = $this->db->fetchAllAssociative(
            'SELECT roster_id, id FROM public.sasb_sleeper_roster WHERE league_id = ? AND roster_id IN (?)',
            [$leagueId, $rosterIds],
            [\Doctrine\DBAL\ParameterType::STRING, ArrayParameterType::INTEGER]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['roster_id']] = (int)$r['id'];
        }
        return $map;
    }

    /** @param SleeperDraftPick[] $chunk */
    private function bulkUpsertDraftPicks(
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
            $params[] = $metadata?->getStatus();
            $params[] = $metadata?->getNumber();
            $params[] = $metadata?->getNewsUpdated();
            $params[] = $metadata?->getInjuryStatus();
            $params[] = $metadata?->getAmount();
        }

        $this->db->executeStatement($sql, $params);
    }
}
