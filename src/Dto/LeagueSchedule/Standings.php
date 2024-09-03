<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;

class Standings {
    /**
     * @var array<int, Rank>
     */
    public array $ranks = [];

    public function getRanks(): array
    {
        return $this->ranks;
    }

    public function setRanks(array $ranks): Standings
    {
        $this->ranks = $ranks;
        return $this;
    }

    public function addRank(Rank $rank): Standings
    {
        $this->ranks[] = $rank;
        return $this;
    }

    public function sortRanks(): Standings
    {
        usort($this->ranks, function (Rank $a, Rank $b) {
            if ($a->getGamesWon() !== $b->getGamesWon()) {
                return -1 * ($a->getGamesWon() <=> $b->getGamesWon());
            }

            if ($a->getGamesDraw() !== $b->getGamesDraw()) {
                return -1 * ($a->getGamesDraw() <=> $b->getGamesDraw());
            }

            if ($a->getPointsFor() !== $b->getPointsFor()) {
                return -1 * ($a->getPointsFor() <=> $b->getPointsFor());
            }

            return -1 * ($b->getPointsAgainst() <=> $a->getPointsAgainst());
        });

        foreach ($this->ranks as $key => $rank) {
            $rank->setRankNumber($key + 1);
            $this->ranks[$key] = $rank;
        }

        return $this;
    }

    public function getRankByRosterId(int $rosterId): ?Rank
    {
        foreach ($this->ranks as $rank) {
            if ($rank->getSleeperRoster()->getRosterId() === $rosterId) {
                return $rank;
            }
        }
        return null;
    }

    public function initMatchupRanks(Matchup $matchup): void
    {
        $rankHome = new Rank();
        $rankHome->setSleeperLeague($matchup->getSleeperMatchupHome()?->getLeague());
        $rankHome->setSleeperRoster($matchup->getSleeperMatchupHome()?->getRoster());
        $rankHome->setSleeperUser($matchup->getSleeperMatchupHome()?->getRoster()?->getOwner());
        $this->addRank($rankHome);

        $rankAway = new Rank();
        $rankAway->setSleeperLeague($matchup->getSleeperMatchupAway()?->getLeague());
        $rankAway->setSleeperRoster($matchup->getSleeperMatchupAway()?->getRoster());
        $rankAway->setSleeperUser($matchup->getSleeperMatchupAway()?->getRoster()?->getOwner());
        $this->addRank($rankAway);
    }

    public function applyMatchupResult(Matchup $matchup, ScheduleWeek $scheduleWeek)
    {
        if (!$matchup->getSleeperMatchupHome() || !$matchup->getSleeperMatchupAway()) {
            return;
        }

        $rankHome = $this->getRankByRosterId($matchup->getSleeperMatchupHome()->getRosterId());
        $rankAway = $this->getRankByRosterId($matchup->getSleeperMatchupAway()->getRosterId());

        if ($matchup->getSleeperMatchupHome()->getEffectivePoints() > $matchup->getSleeperMatchupAway()->getEffectivePoints()) {
            $rankHome->incGamesWon();
            $rankAway->incGamesLost();
        } elseif ($matchup->getSleeperMatchupAway()->getEffectivePoints() > $matchup->getSleeperMatchupHome()->getEffectivePoints()) {
            $rankHome->incGamesLost();
            $rankAway->incGamesWon();
        } else {
            if ($matchup->getSleeperMatchupHome()->getEffectivePoints() > 0 || $matchup->getSleeperMatchupAway()->getEffectivePoints() > 0) {
                $rankHome->incGamesDraw();
                $rankAway->incGamesDraw();
            }
        }

        if ($scheduleWeek->hasMedian) {
            if ($matchup->getSleeperMatchupHome()->getEffectivePoints() > $scheduleWeek->median) {
                $rankHome->incGamesWon();
            } elseif ($matchup->getSleeperMatchupHome()->getEffectivePoints() < $scheduleWeek->median) {
                $rankHome->incGamesLost();
            } elseif ($matchup->getSleeperMatchupHome()->getEffectivePoints() > 0) {
                $rankHome->incGamesDraw();
            }

            if ($matchup->getSleeperMatchupAway()->getEffectivePoints() > $scheduleWeek->median) {
                $rankAway->incGamesWon();
            } elseif ($matchup->getSleeperMatchupAway()->getEffectivePoints() < $scheduleWeek->median) {
                $rankAway->incGamesLost();
            } elseif ($matchup->getSleeperMatchupAway()->getEffectivePoints() > 0) {
                $rankAway->incGamesDraw();
            }
        }

        $rankHome->incPointsFor($matchup->getSleeperMatchupHome()->getEffectivePoints());
        $rankHome->incPointsAgainst($matchup->getSleeperMatchupAway()->getEffectivePoints());
        $rankAway->incPointsFor($matchup->getSleeperMatchupAway()->getEffectivePoints());
        $rankAway->incPointsAgainst($matchup->getSleeperMatchupHome()->getEffectivePoints());
    }
}
