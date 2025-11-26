<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\ScoringTypeEnum;

#[ORM\Embeddable]
class SleeperRosterMetadata {
    #[ORM\Column(nullable: true)]
    private ?string $allowPnInactiveStarters = null;

    #[ORM\Column(nullable: true)]
    private ?string $allowPnPlayerInjuryStatus = null;

    #[ORM\Column(nullable: true)]
    private ?string $allowPnScoring = null;

    #[ORM\Column(nullable: true)]
    private ?string $record = null;

    #[ORM\Column(nullable: true)]
    private ?string $restrictPnScoringStartersOnly = null;

    #[ORM\Column(nullable: true)]
    private ?string $streak = null;

    #[ORM\Column]
    private array $playerNicknames = [];

    public function getAllowPnInactiveStarters(): ?string
    {
        return $this->allowPnInactiveStarters;
    }

    public function setAllowPnInactiveStarters(?string $allowPnInactiveStarters): SleeperRosterMetadata
    {
        $this->allowPnInactiveStarters = $allowPnInactiveStarters;
        return $this;
    }

    public function getAllowPnPlayerInjuryStatus(): ?string
    {
        return $this->allowPnPlayerInjuryStatus;
    }

    public function setAllowPnPlayerInjuryStatus(?string $allowPnPlayerInjuryStatus): SleeperRosterMetadata
    {
        $this->allowPnPlayerInjuryStatus = $allowPnPlayerInjuryStatus;
        return $this;
    }

    public function getAllowPnScoring(): ?string
    {
        return $this->allowPnScoring;
    }

    public function setAllowPnScoring(?string $allowPnScoring): SleeperRosterMetadata
    {
        $this->allowPnScoring = $allowPnScoring;
        return $this;
    }

    public function getRecord(): ?string
    {
        return $this->record;
    }

    public function setRecord(?string $record): SleeperRosterMetadata
    {
        $this->record = $record;
        return $this;
    }

    public function getRestrictPnScoringStartersOnly(): ?string
    {
        return $this->restrictPnScoringStartersOnly;
    }

    public function setRestrictPnScoringStartersOnly(?string $restrictPnScoringStartersOnly): SleeperRosterMetadata
    {
        $this->restrictPnScoringStartersOnly = $restrictPnScoringStartersOnly;
        return $this;
    }

    public function getStreak(): ?string
    {
        return $this->streak;
    }

    public function setStreak(?string $streak): SleeperRosterMetadata
    {
        $this->streak = $streak;
        return $this;
    }

    public function getPlayerNicknames(): array
    {
        return $this->playerNicknames;
    }

    public function setPlayerNicknames(array $playerNicknames): SleeperRosterMetadata
    {
        $this->playerNicknames = $playerNicknames;
        return $this;
    }
}
