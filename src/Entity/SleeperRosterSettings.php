<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class SleeperRosterSettings 
{
    #[ORM\Column]
    private int $wins = 0;

    #[ORM\Column]
    private int $waiverPosition = 0;

    #[ORM\Column]
    private int $waiverBudgetUsed = 0;

    #[ORM\Column]
    private int $totalMoves = 0;

    #[ORM\Column]
    private int $ties = 0;

    #[ORM\Column]
    private int $losses = 0;

    #[ORM\Column]
    private int $fptsDecimal = 0;

    #[ORM\Column]
    private int $fptsAgainstDecimal = 0;

    #[ORM\Column]
    private int $fptsAgainst = 0;

    #[ORM\Column]
    private int $fpts = 0;

    public function getWins(): int
    {
        return $this->wins;
    }

    public function setWins(int $wins): SleeperRosterSettings
    {
        $this->wins = $wins;
        return $this;
    }

    public function getWaiverPosition(): int
    {
        return $this->waiverPosition;
    }

    public function setWaiverPosition(int $waiverPosition): SleeperRosterSettings
    {
        $this->waiverPosition = $waiverPosition;
        return $this;
    }

    public function getWaiverBudgetUsed(): int
    {
        return $this->waiverBudgetUsed;
    }

    public function setWaiverBudgetUsed(int $waiverBudgetUsed): SleeperRosterSettings
    {
        $this->waiverBudgetUsed = $waiverBudgetUsed;
        return $this;
    }

    public function getTotalMoves(): int
    {
        return $this->totalMoves;
    }

    public function setTotalMoves(int $totalMoves): SleeperRosterSettings
    {
        $this->totalMoves = $totalMoves;
        return $this;
    }

    public function getTies(): int
    {
        return $this->ties;
    }

    public function setTies(int $ties): SleeperRosterSettings
    {
        $this->ties = $ties;
        return $this;
    }

    public function getLosses(): int
    {
        return $this->losses;
    }

    public function setLosses(int $losses): SleeperRosterSettings
    {
        $this->losses = $losses;
        return $this;
    }

    public function getFptsDecimal(): int
    {
        return $this->fptsDecimal;
    }

    public function setFptsDecimal(int $fptsDecimal): SleeperRosterSettings
    {
        $this->fptsDecimal = $fptsDecimal;
        return $this;
    }

    public function getFptsAgainstDecimal(): int
    {
        return $this->fptsAgainstDecimal;
    }

    public function setFptsAgainstDecimal(int $fptsAgainstDecimal): SleeperRosterSettings
    {
        $this->fptsAgainstDecimal = $fptsAgainstDecimal;
        return $this;
    }

    public function getFptsAgainst(): int
    {
        return $this->fptsAgainst;
    }

    public function setFptsAgainst(int $fptsAgainst): SleeperRosterSettings
    {
        $this->fptsAgainst = $fptsAgainst;
        return $this;
    }

    public function getFpts(): int
    {
        return $this->fpts;
    }

    public function setFpts(int $fpts): SleeperRosterSettings
    {
        $this->fpts = $fpts;
        return $this;
    }
}
