<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Event;

class SleeperLeagueSyncedEvent
{
    public function __construct(
        public readonly string $leagueId,
    )
    {
    }
}
