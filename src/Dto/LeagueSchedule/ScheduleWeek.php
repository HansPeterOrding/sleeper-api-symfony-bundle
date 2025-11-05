<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;

use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperMatchup;

class ScheduleWeek {
    /**
     * @var array<int, Matchup>
     */
    private array $matchups = [];

    /**
     * @var array<int, PlayoffMatchup>
     */
    private array $playoffMatchups = [];

    /**
     * @var array<int, ByeWeekMatchup>
     */
    private array $byeWeekMatchups = [];

    public ?float $median = null;

    public function __construct(
        public int  $week,
        public bool $hasMedian = false
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

    public function addMatchup(Matchup $matchup)
    {
        $this->matchups[] = $matchup;
    }

    public function getMatchupByRosterId(int $rosterId): ?Matchup
    {
        foreach ($this->matchups as $matchup) {
            if ($matchup->getSleeperMatchupHome()?->getRosterId() === $rosterId) {
                return $matchup;
            } elseif ($matchup->getSleeperMatchupAway()?->getRosterId() === $rosterId) {
                return $matchup;
            }
        }

        return null;
    }

    public function getOwnSleeperMatchupByRosterId(int $rosterId): ?SleeperMatchup
    {
        if (null !== ($matchup = $this->getMatchupByRosterId($rosterId))) {
            return $matchup;
        }

        foreach ($this->playoffMatchups as $playoffMatchup) {
            if ($playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam1() && $playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam1()->getRosterId() === $rosterId) {
                return $playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam1();
            } elseif ($playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam2() && $playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam2()->getRosterId() === $rosterId) {
                return $playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam2();
            }
        }

        foreach ($this->byeWeekMatchups as $byeWeekMatchup) {
            if ($byeWeekMatchup->getSleeperMatchup()->getRosterId() === $rosterId) {
                return $byeWeekMatchup->getSleeperMatchup();
            }
        }

        return null;
    }

    public function getPlayoffMatchups(): array
    {
        return $this->playoffMatchups;
    }

    public function setPlayoffMatchups(array $playoffMatchups): ScheduleWeek
    {
        $this->playoffMatchups = $playoffMatchups;
        return $this;
    }

    public function addPlayoffMatchup(PlayoffMatchup $playoffMatchup)
    {
        $this->playoffMatchups[] = $playoffMatchup;
    }

    public function getByeWeekMatchups(): array
    {
        return $this->byeWeekMatchups;
    }

    public function setByeWeekMatchups(array $byeWeekMatchups): ScheduleWeek
    {
        $this->byeWeekMatchups = $byeWeekMatchups;
        return $this;
    }

    public function addByeWeekMatchup(ByeWeekMatchup $byeWeekMatchup)
    {
        $this->byeWeekMatchups[] = $byeWeekMatchup;
    }

    public function hasMatchup(SleeperMatchup $sleeperMatchup)
    {
        foreach ($this->getMatchups() as $matchup) {
            if ($matchup->getSleeperMatchupHome()->getId() === $sleeperMatchup->getId()) {
                return true;
            }
            if ($matchup->getSleeperMatchupAway()->getId() === $sleeperMatchup->getId()) {
                return true;
            }
        }

        foreach ($this->getPlayoffMatchups() as $playoffMatchup) {
            if ($playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam1() && $playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam1()->getId() === $sleeperMatchup->getId()) {
                return true;
            }
            if ($playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam2() && $playoffMatchup->getSleeperPlayoffMatchup()->getMatchupTeam2()->getId() === $sleeperMatchup->getId()) {
                return true;
            }
        }

        return false;
    }

    public function calculateMedian(): ScheduleWeek
    {
        $fullPoints = [];
        $games = 0;

        foreach ($this->matchups as $matchup) {
            if ($matchup->getSleeperMatchupHome()) {
                $fullPoints[] = $matchup->getSleeperMatchupHome()->getEffectivePoints();
            } else {
                $fullPoints[] = 0;
            }
            if ($matchup->getSleeperMatchupAway()) {
                $fullPoints[] = $matchup->getSleeperMatchupAway()->getEffectivePoints();
            } else {
                $fullPoints[] = 0;
            }
            $games++;
        }

        sort($fullPoints);

        if(array_key_exists($games, $fullPoints) && array_key_exists($games - 1, $fullPoints)) {
            $this->median = ($fullPoints[$games] + $fullPoints[$games - 1]) / 2;
        }

        return $this;
    }

    public function getHighestScorer()
    {
        $highScore = 0;
        $sleeperMatchup = null;

        foreach ($this->matchups as $matchup) {
            if ($matchup->getSleeperMatchupHome()->getEffectivePoints() > $matchup->getSleeperMatchupAway()->getEffectivePoints()) {
                if ($matchup->getSleeperMatchupHome()->getEffectivePoints() > $highScore) {
                    $highScore = $matchup->getSleeperMatchupHome()->getEffectivePoints();
                    $sleeperMatchup = $matchup->getSleeperMatchupHome();
                    continue;
                }
            } else {
                if ($matchup->getSleeperMatchupAway()->getEffectivePoints() > $highScore) {
                    $highScore = $matchup->getSleeperMatchupAway()->getEffectivePoints();
                    $sleeperMatchup = $matchup->getSleeperMatchupAway();
                    continue;
                }
            }
        }

        return $sleeperMatchup;
    }
}
