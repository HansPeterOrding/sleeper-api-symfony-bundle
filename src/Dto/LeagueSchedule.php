<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto;

use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\Matchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\Rank;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\ScheduleWeek;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\Standings;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;

class LeagueSchedule
{
    /**
     * @var array<int, ScheduleWeek>
     */
    private array $scheduleWeeks = [];

    private ?Standings $standings = null;

    public function __construct(
        public SleeperLeague $sleeperLeague
    ) {
    }

    public function getScheduleWeeks(): array
    {
        return $this->scheduleWeeks;
    }

    public function getStandings(): ?Standings
    {
        return $this->standings;
    }
    
    public function addScheduleWeek(ScheduleWeek $scheduleWeek)
    {
        $this->scheduleWeeks[] = $scheduleWeek;
    }

    public function calculateStandings(int $week): Standings
    {
        $this->standings = new Standings();

        for ($currentWeek = 1; $currentWeek <= $week; $currentWeek++) {
            /** @var array<int, Matchup> $matchups */
            $matchups = $this->scheduleWeeks[$currentWeek - 1]->getMatchups();
            foreach ($matchups as $matchup) {
                if ($currentWeek === 1) {
                    $this->standings->initMatchupRanks($matchup);
                }

                $this->standings->applyMatchupResult($matchup);
            }
        }

        $this->standings->sortRanks();

        return $this->standings;
    }
}
