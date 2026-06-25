<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Event;

class SleeperDraftSyncedEvent
{
    public function __construct(
        public readonly string $draftId,
        public readonly ?int   $additionalDraftId,
    )
    {
    }
}
