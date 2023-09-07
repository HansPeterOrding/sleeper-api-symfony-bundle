<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;

use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser;

class Rank
{
    private ?SleeperLeague $sleeperLeague = null;
    private ?SleeperUser   $sleeperUser   = null;
    private ?SleeperRoster $sleeperRoster = null;
    private ?int           $gamesWon      = 0;
    private ?int           $gamesDraw     = 0;
    private ?int           $gamesLost     = 0;
    private ?float         $pointsFor     = 0;
    private ?float         $pointsAgainst = 0;
    private ?int           $rankNumber    = null;
    private ?int           $best          = null;

    public function getSleeperLeague(): ?SleeperLeague
    {
        return $this->sleeperLeague;
    }

    public function setSleeperLeague(?SleeperLeague $sleeperLeague): Rank
    {
        $this->sleeperLeague = $sleeperLeague;
        return $this;
    }

    public function getSleeperUser(): ?SleeperUser
    {
        return $this->sleeperUser;
    }

    public function setSleeperUser(?SleeperUser $sleeperUser): Rank
    {
        $this->sleeperUser = $sleeperUser;
        return $this;
    }

    public function getSleeperRoster(): ?SleeperRoster
    {
        return $this->sleeperRoster;
    }

    public function setSleeperRoster(?SleeperRoster $sleeperRoster): Rank
    {
        $this->sleeperRoster = $sleeperRoster;
        return $this;
    }

    public function getGamesWon(): ?int
    {
        return $this->gamesWon;
    }

    public function setGamesWon(?int $gamesWon): Rank
    {
        $this->gamesWon = $gamesWon;
        return $this;
    }

    public function incGamesWon(int $inc = 1): Rank
    {
        $this->gamesWon += $inc;
        return $this;
    }

    public function getGamesDraw(): ?int
    {
        return $this->gamesDraw;
    }

    public function setGamesDraw(?int $gamesDraw): Rank
    {
        $this->gamesDraw = $gamesDraw;
        return $this;
    }

    public function incGamesDraw(int $inc = 1): Rank
    {
        $this->gamesDraw += $inc;
        return $this;
    }

    public function getGamesLost(): ?int
    {
        return $this->gamesLost;
    }

    public function setGamesLost(?int $gamesLost): Rank
    {
        $this->gamesLost = $gamesLost;
        return $this;
    }

    public function incGamesLost(int $inc = 1): Rank
    {
        $this->gamesLost += $inc;
        return $this;
    }

    public function getPointsFor(): ?float
    {
        return $this->pointsFor;
    }

    public function setPointsFor(?float $pointsFor): Rank
    {
        $this->pointsFor = $pointsFor;
        return $this;
    }

    public function incPointsFor(float $inc): Rank
    {
        $this->pointsFor += $inc;
        return $this;
    }

    public function getPointsAgainst(): ?float
    {
        return $this->pointsAgainst;
    }

    public function setPointsAgainst(?float $pointsAgainst): Rank
    {
        $this->pointsAgainst = $pointsAgainst;
        return $this;
    }

    public function incPointsAgainst(float $inc): Rank
    {
        $this->pointsAgainst += $inc;
        return $this;
    }

    public function getRankNumber(): ?int
    {
        return $this->rankNumber;
    }

    public function setRankNumber(?int $rankNumber): Rank
    {
        $this->rankNumber = $rankNumber;
        return $this;
    }

    public function getBest(): ?int
    {
        return $this->best;
    }

    public function setBest(?int $best): Rank
    {
        $this->best = $best;
        return $this;
    }
}
