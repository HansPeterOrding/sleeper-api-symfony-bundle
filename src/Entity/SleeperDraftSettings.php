<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperLeague as SleeperLeagueDto;

#[ORM\Embeddable]
class SleeperDraftSettings {
    #[ORM\Column(nullable: true)]
    private ?int $teams = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsWr = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsTe = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsSuperFlex = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsRecFlex = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsRb = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsQb = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsLb = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsK = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsIdpFlex = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsFlex = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsDl = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsDb = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsDef = null;

    #[ORM\Column(nullable: true)]
    private ?int $slotsBn = null;

    #[ORM\Column(nullable: true)]
    private ?int $rounds = null;

    #[ORM\Column(nullable: true)]
    private ?int $reversalRound = null;

    #[ORM\Column(nullable: true)]
    private ?int $playerType = null;

    #[ORM\Column(nullable: true)]
    private ?int $pickTimer = null;

    #[ORM\Column(nullable: true)]
    private ?int $nominationTimer = null;

    #[ORM\Column(nullable: true)]
    private ?int $enforcePositionLimits = null;

    #[ORM\Column(nullable: true)]
    private ?int $cpuAutopick = null;

    #[ORM\Column(nullable: true)]
    private ?int $alphaSort = null;

    public function getTeams(): ?int
    {
        return $this->teams;
    }

    public function setTeams(?int $teams): void
    {
        $this->teams = $teams;
    }

    public function getSlotsWr(): ?int
    {
        return $this->slotsWr;
    }

    public function setSlotsWr(?int $slotsWr): void
    {
        $this->slotsWr = $slotsWr;
    }

    public function getSlotsTe(): ?int
    {
        return $this->slotsTe;
    }

    public function setSlotsTe(?int $slotsTe): void
    {
        $this->slotsTe = $slotsTe;
    }

    public function getSlotsSuperFlex(): ?int
    {
        return $this->slotsSuperFlex;
    }

    public function setSlotsSuperFlex(?int $slotsSuperFlex): void
    {
        $this->slotsSuperFlex = $slotsSuperFlex;
    }

    public function getSlotsRecFlex(): ?int
    {
        return $this->slotsRecFlex;
    }

    public function setSlotsRecFlex(?int $slotsRecFlex): void
    {
        $this->slotsRecFlex = $slotsRecFlex;
    }

    public function getSlotsRb(): ?int
    {
        return $this->slotsRb;
    }

    public function setSlotsRb(?int $slotsRb): void
    {
        $this->slotsRb = $slotsRb;
    }

    public function getSlotsQb(): ?int
    {
        return $this->slotsQb;
    }

    public function setSlotsQb(?int $slotsQb): void
    {
        $this->slotsQb = $slotsQb;
    }

    public function getSlotsLb(): ?int
    {
        return $this->slotsLb;
    }

    public function setSlotsLb(?int $slotsLb): void
    {
        $this->slotsLb = $slotsLb;
    }

    public function getSlotsK(): ?int
    {
        return $this->slotsK;
    }

    public function setSlotsK(?int $slotsK): void
    {
        $this->slotsK = $slotsK;
    }

    public function getSlotsIdpFlex(): ?int
    {
        return $this->slotsIdpFlex;
    }

    public function setSlotsIdpFlex(?int $slotsIdpFlex): void
    {
        $this->slotsIdpFlex = $slotsIdpFlex;
    }

    public function getSlotsFlex(): ?int
    {
        return $this->slotsFlex;
    }

    public function setSlotsFlex(?int $slotsFlex): void
    {
        $this->slotsFlex = $slotsFlex;
    }

    public function getSlotsDl(): ?int
    {
        return $this->slotsDl;
    }

    public function setSlotsDl(?int $slotsDl): void
    {
        $this->slotsDl = $slotsDl;
    }

    public function getSlotsDb(): ?int
    {
        return $this->slotsDb;
    }

    public function setSlotsDb(?int $slotsDb): void
    {
        $this->slotsDb = $slotsDb;
    }

    public function getSlotsDef(): ?int
    {
        return $this->slotsDef;
    }

    public function setSlotsDef(?int $slotsDef): void
    {
        $this->slotsDef = $slotsDef;
    }

    public function getSlotsBn(): ?int
    {
        return $this->slotsBn;
    }

    public function setSlotsBn(?int $slotsBn): void
    {
        $this->slotsBn = $slotsBn;
    }

    public function getRounds(): ?int
    {
        return $this->rounds;
    }

    public function setRounds(?int $rounds): void
    {
        $this->rounds = $rounds;
    }

    public function getReversalRound(): ?int
    {
        return $this->reversalRound;
    }

    public function setReversalRound(?int $reversalRound): void
    {
        $this->reversalRound = $reversalRound;
    }

    public function getPlayerType(): ?int
    {
        return $this->playerType;
    }

    public function setPlayerType(?int $playerType): void
    {
        $this->playerType = $playerType;
    }

    public function getPickTimer(): ?int
    {
        return $this->pickTimer;
    }

    public function setPickTimer(?int $pickTimer): void
    {
        $this->pickTimer = $pickTimer;
    }

    public function getNominationTimer(): ?int
    {
        return $this->nominationTimer;
    }

    public function setNominationTimer(?int $nominationTimer): void
    {
        $this->nominationTimer = $nominationTimer;
    }

    public function getEnforcePositionLimits(): ?int
    {
        return $this->enforcePositionLimits;
    }

    public function setEnforcePositionLimits(?int $enforcePositionLimits): void
    {
        $this->enforcePositionLimits = $enforcePositionLimits;
    }

    public function getCpuAutopick(): ?int
    {
        return $this->cpuAutopick;
    }

    public function setCpuAutopick(?int $cpuAutopick): void
    {
        $this->cpuAutopick = $cpuAutopick;
    }

    public function getAlphaSort(): ?int
    {
        return $this->alphaSort;
    }

    public function setAlphaSort(?int $alphaSort): void
    {
        $this->alphaSort = $alphaSort;
    }
}
