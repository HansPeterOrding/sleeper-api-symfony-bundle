<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTradedPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTradedPicksBatchMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperTradedPicksBatchHandler
{
    public function __construct(
        private readonly Connection      $db,
        private readonly LoggerInterface $logger,
    )
    {
    }

    public function __invoke(SyncSleeperTradedPicksBatchMessage $message): void
    {
        if ($message->tradedPicks === []) {
            return;
        }

        $this->db->beginTransaction();
        try {
            $internalDraftId = $this->fetchInternalDraftId($message->draftId);
            $internalLeagueId = $this->fetchInternalLeagueId($message->leagueId);

            $allRosterIds = [];
            $allOwnerIds = [];
            $allPrevOwnerIds = [];

            foreach ($message->tradedPicks as $dto) {
                $allRosterIds[] = $dto->getRosterId();
                $allOwnerIds[] = $dto->getOwnerId();
                $allPrevOwnerIds[] = $dto->getPreviousOwnerId();
            }

            $rosterMap = $this->fetchRosterMap($message->leagueId, array_unique($allRosterIds));
            $userMap = $this->fetchUserMapByRosterId($message->leagueId, array_unique(array_merge($allOwnerIds, $allPrevOwnerIds)));

            foreach (array_chunk($message->tradedPicks, 200) as $chunk) {
                $this->bulkUpsertTradedPicks($chunk, $message->leagueId, $internalDraftId, $internalLeagueId, $rosterMap, $userMap);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->logger->warning('SyncSleeperTradedPicksBatchHandler transient error', [
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

    private function fetchInternalLeagueId(string $leagueId): int
    {
        $id = $this->db->fetchOne(
            'SELECT id FROM public.sasb_sleeper_league WHERE league_id = ?',
            [$leagueId]
        );
        if (!$id) {
            throw new \RuntimeException("League {$leagueId} not found. Run league sync first.");
        }
        return (int)$id;
    }

    /** @return array<int,int> [rosterId => internal id] */
    private function fetchRosterMap(string $leagueId, array $rosterIds): array
    {
        if ($rosterIds === []) return [];
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

    /** @return array<int,int> [rosterId => internal user id] */
    private function fetchUserMapByRosterId(string $leagueId, array $rosterIds): array
    {
        if ($rosterIds === []) return [];
        $rows = $this->db->fetchAllAssociative(
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

    /** @param SleeperTradedPick[] $chunk */
    private function bulkUpsertTradedPicks(
        array  $chunk,
        string $leagueId,
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
            $params[] = $dto->getDraftId();
            // Use message leagueId as authoritative source — DTO leagueId is not always populated
            $params[] = $leagueId;
            $params[] = $internalDraftId;
            $params[] = $internalLeagueId;
            $params[] = $rosterMap[$rosterId] ?? null;
            $params[] = $userMap[$prevOwnerId] ?? null;
            $params[] = $userMap[$ownerId] ?? null;
        }

        $this->db->executeStatement($sql, $params);
    }
}
