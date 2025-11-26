<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats;

final class SyncSleeperPlayerStatsBatch
{
    /**
     * @param SleeperPlayerStats[] $stats
     */
    public function __construct(
        public readonly string $season,
        public readonly ?int $week,
        public readonly array $stats,
    ) {
    }
}
