<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Event\SleeperLeagueSyncedEvent;
use HansPeterOrding\SleeperApiSymfonyBundle\Exception\ImportException;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperLeagueImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperLeagueMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperUsersAndRostersBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Service\SleeperImportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class SyncSleeperLeagueHandler
{
    public function __construct(
        private readonly SleeperApiClientInterface $apiClient,
        private readonly SleeperLeagueImporter     $leagueImporter,
        private readonly MessageBusInterface       $messageBus,
        private readonly SleeperImportService      $importService,
        private readonly EventDispatcherInterface  $eventDispatcher,
        private readonly LoggerInterface           $logger,
    )
    {
    }

    public function __invoke(SyncSleeperLeagueMessage $message): void
    {
        try {
            // 1) Fetch and persist the league
            $leagueEntity = $this->leagueImporter->import($message->leagueId);

            // 2) Fire event — project-level listeners handle SystemLeague connection
            $this->eventDispatcher->dispatch(new SleeperLeagueSyncedEvent($leagueEntity->getLeagueId()));

            $importEntities = $message->importEntities ?? SleeperImportService::getDefaultImportEntities();

            // 3) Fetch users + rosters and dispatch combined batch
            // Draft is dispatched from UsersAndRostersHandler after users/rosters
            // are persisted — ensuring draft picks can connect to them
            if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_USERS_AND_ROSTERS)) {
                $users = $this->apiClient->league()->listUsers($message->leagueId) ?? [];
                $rosters = $this->apiClient->league()->listRosters($message->leagueId) ?? [];

                $this->messageBus->dispatch(new SyncSleeperUsersAndRostersBatchMessage(
                    leagueId: $message->leagueId,
                    users: $users,
                    rosters: $rosters,
                    importEntities: $importEntities,
                ));
            }

        } catch (\Throwable $e) {
            // Transient: network timeouts, API hiccups — log as warning and let Messenger retry
            $this->logger->warning('SyncSleeperLeagueHandler transient error', [
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
