<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperLeague as SleeperLeagueDto;

#[ORM\Entity]
class SleeperLeague
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $totalRosters = null;

    #[ORM\Column]
    private ?string $status = null;

    #[ORM\Column]
    private ?string $sport = null;

    #[ORM\Embedded(class: SleeperLeagueSettings::class, columnPrefix: 'settings_')]
    private SleeperLeagueSettings $settings;

    #[ORM\Column]
    private ?string $seasonType = null;

    #[ORM\Column]
    private ?string $season = null;

    #[ORM\Embedded(class: SleeperLeagueScoringSettings::class, columnPrefix: 'scoring_settings_')]
    private SleeperLeagueScoringSettings $scoringSettings;

    #[ORM\Column(type: 'json')]
    private array $rosterPositions = [];

    #[ORM\Column(nullable: true)]
    private ?string $previousLeagueId = null;

    #[ORM\Column]
    private ?string $name = null;

    #[ORM\Column]
    private ?string $leagueId = null;

    #[ORM\Column]
    private ?string $draftId = null;

    #[ORM\Column(nullable: true)]
    private ?string $avatar = null;

    public function __construct()
    {
        $this->settings = new SleeperLeagueSettings();
        $this->scoringSettings = new SleeperLeagueScoringSettings();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): SleeperLeague
    {
        $this->id = $id;
        return $this;
    }

    public function getTotalRosters(): ?int
    {
        return $this->totalRosters;
    }

    public function setTotalRosters(?int $totalRosters): SleeperLeague
    {
        $this->totalRosters = $totalRosters;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): SleeperLeague
    {
        $this->status = $status;
        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(?string $sport): SleeperLeague
    {
        $this->sport = $sport;
        return $this;
    }

    public function getSettings(): SleeperLeagueSettings
    {
        return $this->settings;
    }

    public function setSettings(SleeperLeagueSettings $settings): SleeperLeague
    {
        $this->settings = $settings;
        return $this;
    }

    public function getSeasonType(): ?string
    {
        return $this->seasonType;
    }

    public function setSeasonType(?string $seasonType): SleeperLeague
    {
        $this->seasonType = $seasonType;
        return $this;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(?string $season): SleeperLeague
    {
        $this->season = $season;
        return $this;
    }

    public function getScoringSettings(): SleeperLeagueScoringSettings
    {
        return $this->scoringSettings;
    }

    public function setScoringSettings(SleeperLeagueScoringSettings $scoringSettings): SleeperLeague
    {
        $this->scoringSettings = $scoringSettings;
        return $this;
    }

    public function getRosterPositions(): array
    {
        return $this->rosterPositions;
    }

    public function setRosterPositions(array $rosterPositions): SleeperLeague
    {
        $this->rosterPositions = $rosterPositions;
        return $this;
    }

    public function getPreviousLeagueId(): ?string
    {
        return $this->previousLeagueId;
    }

    public function setPreviousLeagueId(?string $previousLeagueId): SleeperLeague
    {
        $this->previousLeagueId = $previousLeagueId;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): SleeperLeague
    {
        $this->name = $name;
        return $this;
    }

    public function getLeagueId(): ?string
    {
        return $this->leagueId;
    }

    public function setLeagueId(?string $leagueId): SleeperLeague
    {
        $this->leagueId = $leagueId;
        return $this;
    }

    public function getDraftId(): ?string
    {
        return $this->draftId;
    }

    public function setDraftId(?string $draftId): SleeperLeague
    {
        $this->draftId = $draftId;
        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): SleeperLeague
    {
        $this->avatar = $avatar;
        return $this;
    }

    public function buildFindByCriteriaFromDto(SleeperLeagueDto $sleeperLeagueDto): array
    {
        return [
            'leagueId' => $sleeperLeagueDto->getLeagueId()
        ];
    }
}
