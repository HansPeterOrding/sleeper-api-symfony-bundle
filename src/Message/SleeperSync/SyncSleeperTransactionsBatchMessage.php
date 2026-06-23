<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperTransaction;

readonly class SyncSleeperTransactionsBatchMessage
{
    /**
     * @param SleeperTransaction[] $transactions
     */
    public function __construct(
        public string $leagueId,
        public array  $transactions,
    ) {}
}
