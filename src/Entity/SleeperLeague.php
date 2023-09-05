<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperLeague as SleeperLeagueDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\LeagueStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SeasonTypeEnum;

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
    private LeagueStatusEnum $status;

    #[ORM\Column]
    private ?string $sport = null;

    #[ORM\Embedded(class: SleeperLeagueSettings::class, columnPrefix: 'settings_')]
    private SleeperLeagueSettings $settings;

    #[ORM\Column]
    private SeasonTypeEnum $seasonType;

    #[ORM\Column]
    private ?string $season = null;

    #[ORM\Embedded(class: SleeperLeagueScoringSettings::class, columnPrefix: 'scoring_settings_')]
    private SleeperLeagueScoringSettings $scoringSettings;

    #[ORM\Column(type: 'json')]
    private array $rosterPositions = [];

    #[ORM\Column(nullable: true)]
    private ?string $previousLeagueId = null;

    #[ORM\Column]
    public ?string $name = null;

    #[ORM\Column]
    private ?string $leagueId = null;

    #[ORM\Column(nullable: true)]
    private ?string $draftId = null;

    #[ORM\Column(nullable: true)]
    private ?string $avatar = null;

    /**
     * @var Collection<int, SleeperDraft>
     */
    #[ORM\OneToOne(targetEntity: SleeperDraft::class, mappedBy: 'league')]
    private ?SleeperDraft $draft = null;

    /**
     * @var Collection<int, SleeperRoster>
     */
    #[ORM\OneToMany(targetEntity: SleeperRoster::class, mappedBy: 'league')]
    private Collection $rosters;

    /**
     * @var Collection<int, SleeperTradedPick>
     */
    #[ORM\OneToMany(targetEntity: SleeperTradedPick::class, mappedBy: 'league')]
    private Collection $tradedPicks;

    /**
     * @var Collection<int, SleeperMatchup>
     */
    #[ORM\OneToMany(targetEntity: SleeperMatchup::class, mappedBy: 'league')]
    private Collection $matchups;

    public function __construct()
    {
        $this->settings = new SleeperLeagueSettings();
        $this->scoringSettings = new SleeperLeagueScoringSettings();
        $this->tradedPicks = new ArrayCollection();
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

    public function getStatus(): LeagueStatusEnum
    {
        return $this->status;
    }

    public function setStatus(LeagueStatusEnum $status): SleeperLeague
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

    public function getSeasonType(): SeasonTypeEnum
    {
        return $this->seasonType;
    }

    public function setSeasonType(SeasonTypeEnum $seasonType): SleeperLeague
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

    public function getDraft(): ?SleeperDraft
    {
        return $this->draft;
    }

    public function setDraft(?SleeperDraft $draft): SleeperLeague
    {
        $this->draft = $draft;
        return $this;
    }

    /**
     * @return Collection<int, SleeperRoster>
     */
    public function getRosters(): Collection
    {
        return $this->rosters;
    }

    /**
     * @param Collection<int, SleeperRoster> $rosters
     */
    public function setRosters(Collection $rosters): SleeperLeague
    {
        $this->rosters = $rosters;
        return $this;
    }

    public function addRoster(SleeperRoster $roster): SleeperLeague
    {
        if (!$this->rosters->contains($roster)) {
            $this->rosters[] = $roster;
            $roster->setLeague($this);
        }

        return $this;
    }

    public function removeRoster(SleeperRoster $roster): SleeperLeague
    {
        if ($this->rosters->contains($roster)) {
            $this->rosters->removeElement($roster);

            if ($roster->getLeague() === $this) {
                $roster->setLeague(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SleeperTradedPick>
     */
    public function getTradedPicks(): Collection
    {
        return $this->tradedPicks;
    }

    /**
     * @param Collection<int, SleeperTradedPick> $tradedPicks
     */
    public function setTradedPicks(Collection $tradedPicks): SleeperLeague
    {
        $this->tradedPicks = $tradedPicks;
        return $this;
    }

    public function addTradedPick(SleeperTradedPick $pick): SleeperLeague
    {
        if (!$this->tradedPicks->contains($pick)) {
            $this->tradedPicks[] = $pick;
            $pick->setLeague($this);
        }

        return $this;
    }

    public function removeTradedPick(SleeperTradedPick $pick): SleeperLeague
    {
        if ($this->tradedPicks->contains($pick)) {
            $this->tradedPicks->removeElement($pick);

            if ($pick->getLeague() === $this) {
                $pick->setLeague(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SleeperMatchup>
     */
    public function getMatchups(): Collection
    {
        return $this->matchups;
    }

    /**
     * @param Collection<int, SleeperMatchup> $matchups
     */
    public function setMatchups(Collection $matchups): SleeperRoster
    {
        $this->matchups = $matchups;
        return $this;
    }

    public function addSleeperMatchup(SleeperMatchup $matchup): SleeperRoster
    {
        if (!$this->matchups->contains($matchup)) {
            $this->matchups[] = $matchup;
            $matchup->setLeague($this);
        }

        return $this;
    }

    public function removeSleeperMatchup(SleeperMatchup $matchup): SleeperRoster
    {
        if ($this->matchups->contains($matchup)) {
            $this->matchups->removeElement($matchup);

            if ($matchup->getLeague() === $this) {
                $matchup->setLeague(null);
            }
        }

        return $this;
    }
}
