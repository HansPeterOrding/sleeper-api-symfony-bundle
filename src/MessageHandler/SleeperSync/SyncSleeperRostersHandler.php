<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientInterface;
use HansPeterOrding\SleeperApiClient\Dto\SleeperRoster;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperRostersMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
class SyncSleeperRostersHandler
{
    public function __construct(
        private readonly Connection                $db,
        private readonly SleeperApiClientInterface $apiClient,
        private readonly LoggerInterface           $logger,
    )
    {
    }

    public function __invoke(SyncSleeperRostersMessage $message): void
    {
        try {
            $rosters = $this->apiClient->league()->listRosters($message->leagueId) ?? [];

            if ($rosters === []) {
                return;
            }

            $internalLeagueId = $this->fetchInternalLeagueId($message->leagueId);
            $userMap = $this->fetchUserMap($message->leagueId, $rosters);

            $this->db->beginTransaction();
            try {
                foreach (array_chunk($rosters, 200) as $chunk) {
                    $this->bulkUpsertRosters($chunk, $internalLeagueId, $userMap);
                }
                $this->db->commit();
            } catch (\Throwable $e) {
                $this->db->rollBack();
                throw $e;
            }
        } catch (UnrecoverableMessageHandlingException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $this->logger->warning('SyncSleeperRostersHandler transient error', [
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
            throw new UnrecoverableMessageHandlingException(
                "League {$leagueId} not found. Run league sync first."
            );
        }
        return (int)$id;
    }

    /**
     * @param SleeperRoster[] $rosters
     * @return array<string,int> [ownerId => internal user id]
     */
    private function fetchUserMap(string $leagueId, array $rosters): array
    {
        $ownerIds = array_values(array_filter(array_unique(
            array_map(fn(SleeperRoster $r) => $r->getOwnerId(), $rosters)
        )));

        if ($ownerIds === []) {
            return [];
        }

        $rows = $this->db->fetchAllAssociative(
            'SELECT user_id, id FROM public.sasb_sleeper_user WHERE user_id IN (?)',
            [$ownerIds],
            [ArrayParameterType::STRING]
        );

        $map = [];
        foreach ($rows as $row) {
            $map[$row['user_id']] = (int)$row['id'];
        }
        return $map;
    }

    /** @param SleeperRoster[] $chunk */
    private function bulkUpsertRosters(array $chunk, int $internalLeagueId, array $userMap): void
    {
        if ($chunk === []) {
            return;
        }

        $tuple = '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_roster (
    roster_id, owner_id, league_id, internal_league_id, internal_owner_id,
    starters, reserve, keepers, taxi, players,
    rostersettings_wins, rostersettings_waiver_position, rostersettings_waiver_budget_used,
    rostersettings_total_moves, rostersettings_ties, rostersettings_losses,
    rostersettings_fpts_decimal, rostersettings_fpts_against_decimal,
    rostersettings_fpts_against, rostersettings_fpts,
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
            $params[] = json_encode($dto->getCoOwners());
            $params[] = json_encode($metadata?->getPlayerNicknames() ?? []);
            $params[] = $metadata?->getRecord();
            $params[] = $metadata?->getStreak();
        }

        $this->db->executeStatement($sql, $params);
    }
}
