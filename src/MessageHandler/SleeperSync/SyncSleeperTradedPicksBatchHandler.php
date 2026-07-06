<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTradedPicksBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperTradedPickRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Thin handler: the atomic traded-pick write lives in
 * SleeperTradedPickRepository::pgBulkUpsertTradedPicks() (PostgreSQL-only).
 */
#[AsMessageHandler]
class SyncSleeperTradedPicksBatchHandler
{
    public function __construct(
        private readonly SleeperTradedPickRepository $tradedPickRepository,
        private readonly LoggerInterface             $logger,
    )
    {
    }

    public function __invoke(SyncSleeperTradedPicksBatchMessage $message): void
    {
        if ($message->tradedPicks === []) {
            return;
        }

        try {
            $this->tradedPickRepository->pgBulkUpsertTradedPicks(
                $message->draftId,
                $message->leagueId,
                $message->tradedPicks,
            );
        } catch (\Throwable $e) {
            $this->logger->warning('SyncSleeperTradedPicksBatchHandler transient error', [
                'draftId' => $message->draftId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
