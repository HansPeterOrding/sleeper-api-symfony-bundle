<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjections;

final class SyncSleeperPlayerProjectionsBatch
{
    /**
     * @param SleeperPlayerProjections[] $projections
     */
    public function __construct(
        public readonly string $season,
        public readonly ?int $week,
        public readonly array $projections,
    ) {
    }
}
