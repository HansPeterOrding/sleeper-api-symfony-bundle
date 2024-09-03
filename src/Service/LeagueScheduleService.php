<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Service;

use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\Matchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\ScheduleWeek;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup;

class LeagueScheduleService {
    public function __construct(
        private readonly SleeperLeague $sleeperLeague
    )
    {
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
        $scheduleWeek = new ScheduleWeek($week, boolval($this->sleeperLeague->getSettings()->getLeagueAverageMatch()));

        if ($week < $this->sleeperLeague->getSettings()->getPlayoffWeekStart()) {
            $numTeams = $this->sleeperLeague->getSettings()->getNumTeams();
            $matchupCount = $numTeams / 2;
            for ($matchupId = 1; $matchupId <= $matchupCount; $matchupId++) {
                $scheduleWeek->addMatchup(
                    $this->buildMatchup($week, $matchupId)
                );
            }
        } else {
            foreach ($this->sleeperLeague->getPlayoffMatchups() as $playoffMatchup) {
                if ($playoffMatchup->getR() === $this->sleeperLeague->getSettings()->getPlayoffWeekStart() - $week + 1) {
                    $scheduleWeek->addPlayoffMatchup(
                        $this->buildPlayoffMatchup($playoffMatchup)
                    );
                }
            }

            foreach ($this->sleeperLeague->getMatchups() as $matchup) {
                if ($matchup->getWeek() === $week) {
                    if (!$scheduleWeek->hasMatchup($matchup)) {
                        $byeWeekMatchup = new LeagueSchedule\ByeWeekMatchup();
                        $byeWeekMatchup->setSleeperMatchup($matchup);
                        $scheduleWeek->addByeWeekMatchup($byeWeekMatchup);
                    }
                }
            }
        }


        return $scheduleWeek;
    }

    public function buildMatchup(int $week, int $matchupId): Matchup
    {
        $matchup = new Matchup();

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

    public function buildPlayoffMatchup(SleeperPlayoffMatchup $sleeperPlayoffMatchup): LeagueSchedule\PlayoffMatchup
    {
        $playoffMatchup = new LeagueSchedule\PlayoffMatchup();
        $playoffMatchup->setSleeperPlayoffMatchup($sleeperPlayoffMatchup);

        return $playoffMatchup;
    }
}
