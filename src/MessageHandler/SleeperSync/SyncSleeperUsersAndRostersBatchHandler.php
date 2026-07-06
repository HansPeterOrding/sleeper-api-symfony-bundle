<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperDraftMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperMatchupsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTransactionsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperUsersAndRostersBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperLeagueRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperRosterRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Service\SleeperImportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Thin handler: the atomic users+rosters write lives in
 * SleeperRosterRepository::pgUpsertUsersAndRosters() (PostgreSQL-only). This
 * handler keeps the message API, the cascade orchestration, and the logging.
 */
#[AsMessageHandler]
class SyncSleeperUsersAndRostersBatchHandler
{
    public function __construct(
        private readonly SleeperRosterRepository   $rosterRepository,
        private readonly SleeperLeagueRepository   $leagueRepository,
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
            $this->rosterRepository->pgUpsertUsersAndRosters(
                $message->leagueId,
                $message->users,
                $message->rosters,
            );

            $importEntities = $message->importEntities ?? SleeperImportService::getDefaultImportEntities();

            // Iterate matchups week by week until empty — this determines the authoritative week list
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

                // Fetch transactions using the known week list — empty weeks are valid (no transactions)
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

            // Dispatch draft after users and rosters are persisted
            // so that draft picks can connect to roster and user entities
            if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_DRAFT)) {
                $draftId = $this->leagueRepository->pgFetchDraftId($message->leagueId);
                if ($draftId !== null) {
                    $this->messageBus->dispatch(new SyncSleeperDraftMessage(
                        draftId: $draftId,
                        leagueId: $message->leagueId,
                        importEntities: $importEntities,
                    ));
                }
            }
        } catch (\Throwable $e) {
            $this->logger->warning('SyncSleeperUsersAndRostersBatchHandler transient error', [
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
