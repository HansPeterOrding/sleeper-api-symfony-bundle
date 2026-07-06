<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTransactionsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperTransactionRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Thin handler: the atomic transaction+junction write lives in
 * SleeperTransactionRepository::pgBulkUpsertTransactionsWithJunctions()
 * (PostgreSQL-only).
 */
#[AsMessageHandler]
class SyncSleeperTransactionsBatchHandler
{
    public function __construct(
        private readonly SleeperTransactionRepository $transactionRepository,
    )
    {
    }

    public function __invoke(SyncSleeperTransactionsBatchMessage $message): void
    {
        if ($message->transactions === []) {
            return;
        }

        $this->transactionRepository->pgBulkUpsertTransactionsWithJunctions(
            $message->leagueId,
            $message->transactions,
        );
    }
}
