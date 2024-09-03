<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\ScoringTypeEnum;

#[ORM\Embeddable]
class SleeperDraftMetadata {
    #[ORM\Column]
    private ScoringTypeEnum $scoringType;

    #[ORM\Column(nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?string $elapsedPickTimer = null;

    #[ORM\Column(nullable: true)]
    private ?string $description = null;

    public function getScoringType(): ScoringTypeEnum
    {
        return $this->scoringType;
    }

    public function setScoringType(ScoringTypeEnum $scoringType): void
    {
        $this->scoringType = $scoringType;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getElapsedPickTimer(): ?string
    {
        return $this->elapsedPickTimer;
    }

    public function setElapsedPickTimer(?string $elapsedPickTimer): void
    {
        $this->elapsedPickTimer = $elapsedPickTimer;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
