<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperLeague as SleeperLeagueDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\FantasyPositionEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\InjuryStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\PlayerStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SportEnum;

#[ORM\Embeddable]
class SleeperDraftPickMetadata {
    #[ORM\Column]
    private string $yearsExp;

    #[ORM\Column]
    private string $team;

    #[ORM\Column(nullable: true)]
    private ?PlayerStatusEnum $status = null;

    #[ORM\Column]
    private SportEnum $sport;

    #[ORM\Column]
    private FantasyPositionEnum $position;

    #[ORM\Column]
    private string $playerId;

    #[ORM\Column]
    private string $number;

    #[ORM\Column]
    private ?string $newsUpdated = null;

    #[ORM\Column]
    private string $lastName;

    #[ORM\Column]
    private InjuryStatusEnum $injuryStatus;

    #[ORM\Column]
    private string $firstName;

    #[ORM\Column(nullable: true)]
    private ?string $amount;

    public function getYearsExp(): string
    {
        return $this->yearsExp;
    }

    public function setYearsExp(string $yearsExp): void
    {
        $this->yearsExp = $yearsExp;
    }

    public function getTeam(): string
    {
        return $this->team;
    }

    public function setTeam(string $team): void
    {
        $this->team = $team;
    }

    public function getStatus(): ?PlayerStatusEnum
    {
        return $this->status;
    }

    public function setStatus(?PlayerStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getSport(): SportEnum
    {
        return $this->sport;
    }

    public function setSport(SportEnum $sport): void
    {
        $this->sport = $sport;
    }

    public function getPosition(): FantasyPositionEnum
    {
        return $this->position;
    }

    public function setPosition(FantasyPositionEnum $position): void
    {
        $this->position = $position;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function setPlayerId(string $playerId): void
    {
        $this->playerId = $playerId;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getNewsUpdated(): ?string
    {
        return $this->newsUpdated;
    }

    public function setNewsUpdated(?string $newsUpdated): void
    {
        $this->newsUpdated = $newsUpdated;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getInjuryStatus(): InjuryStatusEnum
    {
        return $this->injuryStatus;
    }

    public function setInjuryStatus(InjuryStatusEnum $injuryStatus): void
    {
        $this->injuryStatus = $injuryStatus;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): SleeperDraftPickMetadata
    {
        $this->amount = $amount;
        return $this;
    }
}
