<?php

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperNflState as SleeperNflStateDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SeasonTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperNflStateRepository;

#[ORM\Entity(repositoryClass: SleeperNflStateRepository::class)]
class SleeperNflState
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $week = null;

    #[ORM\Column(enumType: SeasonTypeEnum::class)]
    private ?SeasonTypeEnum $seasonType = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $seasonStartDate = null;

    #[ORM\Column(length: 255)]
    private ?string $season = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $previousSeason = null;

    #[ORM\Column]
    private ?int $leg = null;

    #[ORM\Column(length: 255)]
    private ?string $leagueSeason = null;

    #[ORM\Column(length: 255)]
    private ?string $leagueCreateSeason = null;

    #[ORM\Column]
    private ?int $displayWeek = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(int $week): static
    {
        $this->week = $week;

        return $this;
    }

    public function getSeasonType(): ?SeasonTypeEnum
    {
        return $this->seasonType;
    }

    public function setSeasonType(SeasonTypeEnum $seasonType): static
    {
        $this->seasonType = $seasonType;

        return $this;
    }

    public function getSeasonStartDate(): ?\DateTime
    {
        return $this->seasonStartDate;
    }

    public function setSeasonStartDate(?\DateTime $seasonStartDate): static
    {
        $this->seasonStartDate = $seasonStartDate;

        return $this;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(string $season): static
    {
        $this->season = $season;

        return $this;
    }

    public function getPreviousSeason(): ?string
    {
        return $this->previousSeason;
    }

    public function setPreviousSeason(?string $previousSeason): static
    {
        $this->previousSeason = $previousSeason;

        return $this;
    }

    public function getLeg(): ?int
    {
        return $this->leg;
    }

    public function setLeg(int $leg): static
    {
        $this->leg = $leg;

        return $this;
    }

    public function getLeagueSeason(): ?string
    {
        return $this->leagueSeason;
    }

    public function setLeagueSeason(string $leagueSeason): static
    {
        $this->leagueSeason = $leagueSeason;

        return $this;
    }

    public function getLeagueCreateSeason(): ?string
    {
        return $this->leagueCreateSeason;
    }

    public function setLeagueCreateSeason(string $leagueCreateSeason): static
    {
        $this->leagueCreateSeason = $leagueCreateSeason;

        return $this;
    }

    public function getDisplayWeek(): ?int
    {
        return $this->displayWeek;
    }

    public function setDisplayWeek(int $displayWeek): static
    {
        $this->displayWeek = $displayWeek;

        return $this;
    }

    public function buildFindByCriteriaFromDto(SleeperNflStateDto $sleeperNflStateDto): array
    {
        return [
            'season' => $sleeperNflStateDto->getSeason()
        ];
    }
}
