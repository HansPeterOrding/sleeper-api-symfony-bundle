<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayoffMatchupsBatchMessage;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayoffMatchupRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Thin handler: the atomic playoff-bracket write lives in
 * SleeperPlayoffMatchupRepository::pgBulkUpsertPlayoffMatchups() (PostgreSQL-only).
 */
#[AsMessageHandler]
class SyncSleeperPlayoffMatchupsBatchHandler
{
    public function __construct(
        private readonly SleeperPlayoffMatchupRepository $playoffMatchupRepository,
        private readonly LoggerInterface                 $logger,
    )
    {
    }

    public function __invoke(SyncSleeperPlayoffMatchupsBatchMessage $message): void
    {
        if ($message->winnersData === [] && $message->losersData === []) {
            return;
        }

        try {
            $this->playoffMatchupRepository->pgBulkUpsertPlayoffMatchups(
                $message->leagueId,
                $message->winnersData,
                $message->losersData,
            );
        } catch (\Throwable $e) {
            $this->logger->warning('SyncSleeperPlayoffMatchupsBatchHandler transient error', [
                'leagueId' => $message->leagueId,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
