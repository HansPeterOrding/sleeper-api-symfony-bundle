<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

readonly class SyncSleeperPlayersBatchMessage
{
    /**
     * @param array[] $players Raw player arrays from Sleeper API (not DTOs)
     */
    public function __construct(
        public array $players,
    ) {}
}
