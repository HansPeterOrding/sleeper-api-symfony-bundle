<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperTradedPick;

readonly class SyncSleeperTradedPicksBatchMessage
{
    /**
     * @param SleeperTradedPick[] $tradedPicks
     */
    public function __construct(
        public string $draftId,
        public string $leagueId,
        public array  $tradedPicks,
        public ?array $importEntities = null,
    ) {}
}
