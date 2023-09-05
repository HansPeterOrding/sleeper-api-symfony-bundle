<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;

use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperMatchup;

class Matchup
{
    private ?SleeperMatchup $sleeperMatchupHome = null;

    private ?SleeperMatchup $sleeperMatchupAway = null;

    public function getSleeperMatchupHome(): ?SleeperMatchup
    {
        return $this->sleeperMatchupHome;
    }

    public function setSleeperMatchupHome(?SleeperMatchup $sleeperMatchupHome): Matchup
    {
        $this->sleeperMatchupHome = $sleeperMatchupHome;
        return $this;
    }

    public function getSleeperMatchupAway(): ?SleeperMatchup
    {
        return $this->sleeperMatchupAway;
    }

    public function setSleeperMatchupAway(?SleeperMatchup $sleeperMatchupAway): Matchup
    {
        $this->sleeperMatchupAway = $sleeperMatchupAway;
        return $this;
    }
}
