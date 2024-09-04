<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup;

class SyncSleeperMatchup
{
    public function __construct(
        public readonly string $leagueId,
        public readonly int $week,
        public readonly SleeperMatchup $matchup
    ) {
    }
}
