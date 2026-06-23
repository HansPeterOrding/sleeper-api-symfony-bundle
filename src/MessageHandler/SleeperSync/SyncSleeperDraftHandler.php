<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\AdditionalDraft;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft;
use HansPeterOrding\SleeperApiSymfonyBundle\Exception\ImportException;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperDraftImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperDraftMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperDraftPicksBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTradedPicksBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperLeagueRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Service\SleeperImportService;
use Psr\Log\LoggerInterface;
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
        private readonly LoggerInterface           $logger,
    )
    {
    }

    public function __invoke(SyncSleeperDraftMessage $message): void
    {
        // Unrecoverable: parent league missing — retrying will never help
        $leagueEntity = $this->leagueRepository->findOneBy(['leagueId' => $message->leagueId]);
        if ($leagueEntity === null) {
            throw new UnrecoverableMessageHandlingException(
                sprintf('League %s not found in DB. Run league sync first.', $message->leagueId)
            );
        }

        try {
            // 2) Fetch and persist draft
            $draftEntity = $this->draftImporter->import($message->draftId, $leagueEntity);

            // 3) Connect to AdditionalDraft if applicable
            if ($message->additionalDraftId !== null) {
                $this->connectAdditionalDraft($draftEntity, $message->additionalDraftId);
            }

            $importEntities = $message->importEntities ?? SleeperImportService::getDefaultImportEntities();

            // 4) Fetch picks and dispatch
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

            // 5) Fetch traded picks and dispatch
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
            // Transient: network timeouts, API hiccups — log as warning and let Messenger retry
            $this->logger->warning('SyncSleeperDraftHandler transient error', [
                'draftId' => $message->draftId,
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function connectAdditionalDraft(SleeperDraft $draftEntity, int $additionalDraftId): void
    {
        $additionalDraft = $this->entityManager->find(AdditionalDraft::class, $additionalDraftId);
        if ($additionalDraft === null) {
            throw new UnrecoverableMessageHandlingException(
                sprintf('AdditionalDraft %d not found in DB.', $additionalDraftId)
            );
        }

        $additionalDraft->setSleeperDraft($draftEntity);
        $this->entityManager->persist($additionalDraft);
        $this->entityManager->flush();
    }
}
