<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;

use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup;

class PlayoffMatchup {
    private ?SleeperPlayoffMatchup $sleeperPlayoffMatchup = null;

    public function getSleeperPlayoffMatchup(): ?SleeperPlayoffMatchup
    {
        return $this->sleeperPlayoffMatchup;
    }

    public function setSleeperPlayoffMatchup(?SleeperPlayoffMatchup $sleeperPlayoffMatchup): PlayoffMatchup
    {
        $this->sleeperPlayoffMatchup = $sleeperPlayoffMatchup;
        return $this;
    }
}
