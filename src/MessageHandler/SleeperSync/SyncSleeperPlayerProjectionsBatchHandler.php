<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerProjectionsBatch;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerProjectionsRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Thin by design: all SQL lives in
 * SleeperPlayerProjectionsRepository::pgBulkUpsertProjections() (pg-prefixed
 * repository method convention). This handler only unpacks the message and
 * delegates — no player-id resolution, no row building, no SQL here any more
 * (that used to live in the now-removed AbstractSyncStatsHandler).
 */
#[AsMessageHandler]
final class SyncSleeperPlayerProjectionsBatchHandler
{
    public function __construct(
        private readonly SleeperPlayerProjectionsRepository $sleeperPlayerProjectionsRepository,
    )
    {
    }

    public function __invoke(SyncSleeperPlayerProjectionsBatch $message): void
    {
        if ($message->projections === []) {
            return;
        }

        $this->sleeperPlayerProjectionsRepository->pgBulkUpsertProjections(
            $message->season,
            $message->week,
            $message->projections,
        );
    }
}
