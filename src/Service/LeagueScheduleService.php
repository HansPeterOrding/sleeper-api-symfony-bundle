<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Service;

use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\Matchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\ScheduleWeek;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;

class LeagueScheduleService
{
    public function __construct(
        private readonly SleeperLeague $sleeperLeague
    ) {
    }

    public function buildLeagueSchedule(int $weeks = 18): LeagueSchedule
    {
        $leagueSchedule = new LeagueSchedule($this->sleeperLeague);

        for ($week = 1; $week <= $weeks; $week++) {
            $scheduleWeek = $this->buildScheduleWeek($week);
            $leagueSchedule->addScheduleWeek($scheduleWeek);
        }

        return $leagueSchedule;
    }

    public function buildScheduleWeek(int $week): ScheduleWeek
    {
        $scheduleWeek = new ScheduleWeek($week);

        $numTeams = $this->sleeperLeague->getSettings()->getNumTeams();
        $matchupCount = $numTeams / 2;
        for ($matchupId = 1; $matchupId <= $matchupCount; $matchupId++) {
            $scheduleWeek->addMatchup(
                $this->buildMatchup($week, $matchupId)
            );
        }

        return $scheduleWeek;
    }

    public function buildMatchup(int $week, int $matchupId): Matchup
    {
        $matchup = new Matchup($week);

        foreach ($this->sleeperLeague->getMatchups() as $sleeperMatchup) {
            if ($sleeperMatchup->getWeek() === $week && $sleeperMatchup->getMatchupId() === $matchupId) {
                if (!$matchup->getSleeperMatchupHome()) {
                    $matchup->setSleeperMatchupHome($sleeperMatchup);
                } else {
                    $matchup->setSleeperMatchupAway($sleeperMatchup);
                }
            }
        }

        return $matchup;
    }
}
