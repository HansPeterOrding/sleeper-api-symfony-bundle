<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperRoster;

readonly class SyncSleeperRostersBatchMessage
{
    /**
     * @param SleeperRoster[] $rosters
     */
    public function __construct(
        public string $leagueId,
        public array  $rosters,
        public ?array $importEntities = null,
    ) {}
}
