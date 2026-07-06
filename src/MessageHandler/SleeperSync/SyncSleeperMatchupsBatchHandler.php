<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperMatchupsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayoffMatchupsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperMatchupRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Service\SleeperImportService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Thin handler: the atomic matchup+junction write lives in
 * SleeperMatchupRepository::pgBulkUpsertMatchupsWithJunctions() (PostgreSQL-only).
 */
#[AsMessageHandler]
class SyncSleeperMatchupsBatchHandler
{
    public function __construct(
        private readonly SleeperMatchupRepository  $matchupRepository,
        private readonly SleeperApiClientInterface $apiClient,
        private readonly MessageBusInterface       $messageBus,
        private readonly SleeperImportService      $importService,
        private readonly LoggerInterface           $logger,
    )
    {
    }

    public function __invoke(SyncSleeperMatchupsBatchMessage $message): void
    {
        if ($message->matchups === []) {
            return;
        }

        try {
            $this->matchupRepository->pgBulkUpsertMatchupsWithJunctions(
                $message->leagueId,
                $message->matchups,
            );
        } catch (\Throwable $e) {
            $this->logger->warning('SyncSleeperMatchupsBatchHandler transient error', [
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        // Dispatch playoff matchups if flag is set — runs after commit so matchups are guaranteed
        $importEntities = $message->importEntities;
        if ($this->importService->shouldImport($importEntities, SleeperImportService::IMPORT_ENTITY_PLAYOFF_MATCHUPS)) {
            $winnersData = $this->apiClient->league()->listPlayoffMatchups(
                $message->leagueId, AbstractEndpoint::BRANCH_WINNERS
            ) ?? [];
            $losersData = $this->apiClient->league()->listPlayoffMatchups(
                $message->leagueId, AbstractEndpoint::BRANCH_LOSERS
            ) ?? [];

            if ($winnersData !== [] || $losersData !== []) {
                $this->messageBus->dispatch(new SyncSleeperPlayoffMatchupsBatchMessage(
                    leagueId: $message->leagueId,
                    winnersData: $winnersData,
                    losersData: $losersData,
                ));
            }
        }
    }
}
