<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

readonly class SyncSleeperLeagueMessage
{
    public function __construct(
        public string $leagueId,
        public ?array $importEntities = null,
    ) {}
}
