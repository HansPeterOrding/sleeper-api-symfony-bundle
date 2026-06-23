<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync;

readonly class SyncSleeperDraftMessage
{
    public function __construct(
        public string $draftId,
        public string $leagueId,
        public ?int   $additionalDraftId = null,
        public ?array $importEntities = null,
    ) {}
}
