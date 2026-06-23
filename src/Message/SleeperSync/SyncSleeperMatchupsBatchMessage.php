<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup;

readonly class SyncSleeperMatchupsBatchMessage
{
    /**
     * @param array<int, SleeperMatchup[]> $matchups week => matchups[]
     */
    public function __construct(
        public string $leagueId,
        public array  $matchups,
        public ?array $importEntities = null,
    ) {}
}
