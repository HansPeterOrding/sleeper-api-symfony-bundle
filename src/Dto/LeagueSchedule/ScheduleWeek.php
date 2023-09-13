<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;

class ScheduleWeek
{
    /**
     * @var array<int, Matchup>
     */
    private array $matchups;

    public ?float $median = null;

    public function __construct(
        public int $week,
        public bool $hasMedian = false
    ) {
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
            if ($matchup->getSleeperMatchupHome()->getRosterId() === $rosterId) {
                return $matchup;
            } elseif ($matchup->getSleeperMatchupAway()->getRosterId() === $rosterId) {
                return $matchup;
            }
        }

        return null;
    }

    public function calculateMedian(): ScheduleWeek
    {
        $fullPoints = [];
        $games = 0;

        foreach($this->matchups as $matchup) {
            $fullPoints[] = $matchup->getSleeperMatchupHome()->getEffectivePoints();
            $fullPoints[] = $matchup->getSleeperMatchupAway()->getEffectivePoints();
            $games++;
        }

        $this->median = ($fullPoints[$games] + $fullPoints[$games-1]) / 2;

        return $this;
    }
}
