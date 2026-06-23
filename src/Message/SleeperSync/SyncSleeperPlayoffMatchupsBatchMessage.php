<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayoffMatchup;

readonly class SyncSleeperPlayoffMatchupsBatchMessage
{
    /**
     * @param SleeperPlayoffMatchup[] $winnersData
     * @param SleeperPlayoffMatchup[] $losersData
     */
    public function __construct(
        public string $leagueId,
        public array  $winnersData,
        public array  $losersData,
        public ?array $importEntities = null,
    ) {}
}
