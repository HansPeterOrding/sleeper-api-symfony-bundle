<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperDraftPicksBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperDraftPickRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Thin handler: the atomic draft-pick write lives in
 * SleeperDraftPickRepository::pgBulkUpsertDraftPicks() (PostgreSQL-only).
 */
#[AsMessageHandler]
class SyncSleeperDraftPicksBatchHandler
{
    public function __construct(
        private readonly SleeperDraftPickRepository $draftPickRepository,
        private readonly LoggerInterface            $logger,
    )
    {
    }

    public function __invoke(SyncSleeperDraftPicksBatchMessage $message): void
    {
        if ($message->picks === []) {
            return;
        }

        try {
            $this->draftPickRepository->pgBulkUpsertDraftPicks(
                $message->draftId,
                $message->picks,
            );
        } catch (\Throwable $e) {
            $this->logger->warning('SyncSleeperDraftPicksBatchHandler transient error', [
                'draftId' => $message->draftId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
