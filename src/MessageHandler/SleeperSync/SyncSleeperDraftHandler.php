<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Event\SleeperDraftSyncedEvent;
use HansPeterOrding\SleeperApiSymfonyBundle\Exception\ImportException;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperDraftImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperDraftMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperDraftPicksBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTradedPicksBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperLeagueRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Service\SleeperImportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class SyncSleeperDraftHandler
{
    public function __construct(
        private readonly SleeperApiClientInterface $apiClient,
        private readonly SleeperDraftImporter      $draftImporter,
        private readonly SleeperLeagueRepository   $leagueRepository,
        private readonly EntityManagerInterface    $entityManager,
        private readonly MessageBusInterface       $messageBus,
        private readonly SleeperImportService      $importService,
        private readonly EventDispatcherInterface  $eventDispatcher,
        private readonly LoggerInterface           $logger,
    )
    {
    }

    public function __invoke(SyncSleeperDraftMessage $message): void
    {
        // For additional drafts, do not connect to the league entity —
        // the AdditionalDraft entity is the connection point, not SleeperLeague.
        // Connecting would violate the OneToOne unique constraint on internal_league_id.
        $isAdditionalDraft = $message->additionalDraftId !== null;

        $leagueEntity = null;
        if (!$isAdditionalDraft) {
            $leagueEntity = $this->leagueRepository->findOneBy(['leagueId' => $message->leagueId]);
            if ($leagueEntity === null) {
                throw new UnrecoverableMessageHandlingException(
                    sprintf('League %s not found in DB. Run league sync first.', $message->leagueId)
                );
            }
        }

        try {
            // 1) Fetch and persist draft — pass null league for additional drafts
            $draftEntity = $this->draftImporter->import($message->draftId, $leagueEntity);

            // 2) Fire event — project-level listener handles AdditionalDraft connection
            $this->eventDispatcher->dispatch(new SleeperDraftSyncedEvent(
                draftId: $draftEntity->getDraftId(),
                additionalDraftId: $message->additionalDraftId,
            ));

            $importEntities = $message->importEntities ?? SleeperImportService::getDefaultImportEntities();

            // 3) Fetch picks and dispatch
            if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_DRAFT_PICKS)) {
                $picks = $this->apiClient->draft()->listPicks($message->draftId) ?? [];
                if ($picks !== []) {
                    $this->messageBus->dispatch(new SyncSleeperDraftPicksBatchMessage(
                        draftId: $message->draftId,
                        picks: $picks,
                        importEntities: $importEntities,
                    ));
                }
            }

            // 4) Fetch traded picks and dispatch
            if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_TRADED_PICKS)) {
                $tradedPicks = $this->apiClient->draft()->listTradedPicks($message->draftId) ?? [];
                if ($tradedPicks !== []) {
                    $this->messageBus->dispatch(new SyncSleeperTradedPicksBatchMessage(
                        draftId: $message->draftId,
                        leagueId: $message->leagueId,
                        tradedPicks: $tradedPicks,
                        importEntities: $importEntities,
                    ));
                }
            }

        } catch (\Throwable $e) {
            $this->logger->warning('SyncSleeperDraftHandler transient error', [
                'draftId' => $message->draftId,
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
