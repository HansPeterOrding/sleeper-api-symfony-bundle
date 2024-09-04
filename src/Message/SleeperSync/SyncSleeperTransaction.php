<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTransaction;

class SyncSleeperTransaction
{
    public function __construct(
        public readonly string $leagueId,
        public readonly SleeperTransaction $transaction
    ) {
    }
}
