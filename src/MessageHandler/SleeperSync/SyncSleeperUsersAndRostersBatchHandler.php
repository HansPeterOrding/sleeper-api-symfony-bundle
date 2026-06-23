<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientInterface;
use HansPeterOrding\SleeperApiClient\Dto\SleeperRoster;
use HansPeterOrding\SleeperApiClient\Dto\SleeperUser;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperDraftMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperMatchupsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTransactionsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperUsersAndRostersBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Service\SleeperImportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class SyncSleeperUsersAndRostersBatchHandler
{
    public function __construct(
        private readonly Connection                $db,
        private readonly SleeperApiClientInterface $apiClient,
        private readonly MessageBusInterface       $messageBus,
        private readonly SleeperImportService      $importService,
        private readonly LoggerInterface           $logger,
    )
    {
    }

    public function __invoke(SyncSleeperUsersAndRostersBatchMessage $message): void
    {
        try {
            $this->db->beginTransaction();

            $internalLeagueId = $this->fetchInternalLeagueId($message->leagueId);

            // 1) Upsert users first
            foreach (array_chunk($message->users, 200) as $chunk) {
                $this->bulkUpsertUsers($chunk);
            }

            // 2) Build user map for roster owner connections
            $userMap = $this->fetchUserMap($message->leagueId, $message->rosters);

            // 3) Upsert rosters with owner connections
            foreach (array_chunk($message->rosters, 200) as $chunk) {
                $this->bulkUpsertRosters($chunk, $internalLeagueId, $userMap);
            }

            $this->db->commit();

            $importEntities = $message->importEntities ?? SleeperImportService::getDefaultImportEntities();

            // 4) Iterate matchups week by week until empty — this determines the authoritative week list
            if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_MATCHUPS)) {
                $weeklyMatchups = [];
                $week = 1;
                while (true) {
                    $matchups = $this->apiClient->league()->listMatchups($message->leagueId, $week) ?? [];
                    if ($matchups === []) {
                        break;
                    }
                    $weeklyMatchups[$week] = $matchups;
                    $week++;
                }

                if ($weeklyMatchups !== []) {
                    $this->messageBus->dispatch(new SyncSleeperMatchupsBatchMessage(
                        leagueId: $message->leagueId,
                        matchups: $weeklyMatchups,
                        importEntities: $importEntities,
                    ));
                }

                // 5) Fetch transactions using the known week list — empty weeks are valid (no transactions)
                if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_TRANSACTIONS)) {
                    $knownWeeks = array_keys($weeklyMatchups);
                    $allTransactions = [];
                    foreach ($knownWeeks as $w) {
                        $transactions = $this->apiClient->league()->listTransactions($message->leagueId, $w) ?? [];
                        foreach ($transactions as $t) {
                            $allTransactions[] = $t;
                        }
                    }

                    if ($allTransactions !== []) {
                        $this->messageBus->dispatch(new SyncSleeperTransactionsBatchMessage(
                            leagueId: $message->leagueId,
                            transactions: $allTransactions,
                        ));
                    }
                }
            }

            // 6) Dispatch draft after users and rosters are persisted
            // so that draft picks can connect to roster and user entities
            if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_DRAFT)) {
                $draftId = $this->fetchDraftId($message->leagueId);
                if ($draftId !== null) {
                    $this->messageBus->dispatch(new SyncSleeperDraftMessage(
                        draftId: $draftId,
                        leagueId: $message->leagueId,
                        importEntities: $importEntities,
                    ));
                }
            }
        } catch (\Throwable $e) {
            if ($this->db->isTransactionActive()) {
                $this->db->rollBack();
            }
            $this->logger->warning('SyncSleeperUsersAndRostersBatchHandler transient error', [
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function fetchDraftId(string $leagueId): ?string
    {
        $draftId = $this->db->fetchOne(
            'SELECT draft_id FROM public.sasb_sleeper_league WHERE league_id = ?',
            [$leagueId]
        );
        return $draftId ?: null;
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

    /** @param SleeperUser[] $chunk */
    private function bulkUpsertUsers(array $chunk): void
    {
        if ($chunk === []) {
            return;
        }

        $tuple = '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_user (
    user_id, username, display_name, avatar, real_name,
    is_bot, email, phone, pending, solicitable,
    verification, token, summoner_name, summoner_region,
    metadata_team_name
) VALUES {$valuesSql}
ON CONFLICT (user_id) DO UPDATE SET
    username         = EXCLUDED.username,
    display_name     = EXCLUDED.display_name,
    avatar           = EXCLUDED.avatar,
    real_name        = EXCLUDED.real_name,
    is_bot           = EXCLUDED.is_bot,
    email            = EXCLUDED.email,
    phone            = EXCLUDED.phone,
    pending          = EXCLUDED.pending,
    solicitable      = EXCLUDED.solicitable,
    verification     = EXCLUDED.verification,
    token            = EXCLUDED.token,
    summoner_name    = EXCLUDED.summoner_name,
    summoner_region  = EXCLUDED.summoner_region,
    metadata_team_name = EXCLUDED.metadata_team_name
SQL;

        $params = [];
        foreach ($chunk as $dto) {
            $params[] = $dto->getUserId();
            $params[] = $dto->getUsername();
            $params[] = $dto->getDisplayName();
            $params[] = $dto->getAvatar();
            $params[] = $dto->getRealName();
            $params[] = $dto->getIsBot() ? 'true' : 'false';
            $params[] = $dto->getEmail();
            $params[] = $dto->getPhone();
            $params[] = $dto->getPending();
            $params[] = $dto->getSolicitable();
            $params[] = $dto->getVerification();
            $params[] = $dto->getToken();
            $params[] = $dto->getSummonerName();
            $params[] = $dto->getSummonerRegion();
            $params[] = $dto->getMetadata()?->getTeamName();
        }

        $this->db->executeStatement($sql, $params);
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
