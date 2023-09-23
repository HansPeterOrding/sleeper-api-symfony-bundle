<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto;

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

    public function getScheduleWeekByWeek(int $week): ?ScheduleWeek
    {
        foreach ($this->scheduleWeeks as $scheduleWeek) {
            if ($scheduleWeek->week === $week) {
                return $scheduleWeek;
            }
        }

        return null;
    }

    public function getStandings(): ?Standings
    {
        return $this->standings;
    }

    public function addScheduleWeek(ScheduleWeek $scheduleWeek)
    {
        if ($scheduleWeek->hasMedian) {
            $scheduleWeek->calculateMedian();
        }
        $this->scheduleWeeks[] = $scheduleWeek;
    }

    public function calculateStandings(int $week): Standings
    {
        $this->standings = new Standings();

        for ($currentWeek = 1; $currentWeek <= $week; $currentWeek++) {
            $this->applyWeek($currentWeek);
        }

        $this->standings->sortRanks();

        return $this->standings;
    }

    public function applyWeek(int $week): void
    {
        $scheduleWeek = $this->scheduleWeeks[$week - 1];
        $matchups = $scheduleWeek->getMatchups();
        foreach ($matchups as $matchup) {
            if ($week === 1) {
                $this->standings->initMatchupRanks($matchup);
            }

            $this->standings->applyMatchupResult($matchup, $scheduleWeek);
        }
    }
}
