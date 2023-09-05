<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;

class ScheduleWeek
{
    private array $matchups;

    public function __construct(
        public readonly int $week
    )
    {
    }

    public function getMatchups(): array
    {
        return $this->matchups;
    }

    public function setMatchups(array $matchups): ScheduleWeek
    {
        $this->matchups = $matchups;
        return $this;
    }

    public function addMatchup(Matchup $matchup) {
        $this->matchups[] = $matchup;
    }
}
