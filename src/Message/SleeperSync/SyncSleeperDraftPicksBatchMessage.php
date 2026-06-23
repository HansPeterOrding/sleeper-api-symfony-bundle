<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftPick;

readonly class SyncSleeperDraftPicksBatchMessage
{
    /**
     * @param SleeperDraftPick[] $picks
     */
    public function __construct(
        public string $draftId,
        public array  $picks,
        public ?array $importEntities = null,
    ) {}
}
