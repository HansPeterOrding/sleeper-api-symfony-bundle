<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup;

final class SyncSleeperMatchupBatch
{
    public function __construct(
        public readonly string $leagueId,
        /**
         * @var SleeperMatchup[][] $matchups
         */
        public readonly array $matchups
    )
    {
    }
}
