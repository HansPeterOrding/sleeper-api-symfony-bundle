<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerStatsBatch;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerStatsRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Thin by design: all SQL lives in SleeperPlayerStatsRepository::pgBulkUpsertStats()
 * (pg-prefixed repository method convention). This handler only unpacks the
 * message and delegates — no player-id resolution, no row building, no SQL
 * here any more (that used to live in the now-removed AbstractSyncStatsHandler).
 */
#[AsMessageHandler]
final class SyncSleeperPlayerStatsBatchHandler
{
    public function __construct(
        private readonly SleeperPlayerStatsRepository $sleeperPlayerStatsRepository,
    )
    {
    }

    public function __invoke(SyncSleeperPlayerStatsBatch $message): void
    {
        if ($message->stats === []) {
            return;
        }

        $this->sleeperPlayerStatsRepository->pgBulkUpsertStats(
            $message->season,
            $message->week,
            $message->stats,
        );
    }
}
