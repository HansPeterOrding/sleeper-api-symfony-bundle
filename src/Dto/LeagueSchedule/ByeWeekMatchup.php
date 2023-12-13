<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;

use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperMatchup;

class ByeWeekMatchup
{
    private ?SleeperMatchup $sleeperMatchup = null;

    public function getSleeperMatchup(): ?SleeperMatchup
    {
        return $this->sleeperMatchup;
    }

    public function setSleeperMatchup(?SleeperMatchup $sleeperMatchup): ByeWeekMatchup
    {
        $this->sleeperMatchup = $sleeperMatchup;
        return $this;
    }
}
