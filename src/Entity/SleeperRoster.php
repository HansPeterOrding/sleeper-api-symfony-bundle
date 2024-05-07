<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\User;
use HansPeterOrding\SleeperApiClient\Dto\SleeperRoster as SleeperRosterDto;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'sasb_sleeper_roster_unique', columns: ['league_id', 'roster_id'])]
class SleeperRoster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private int $rosterId;

    #[ORM\Column(nullable: true)]
    private ?string $ownerId;

    #[ORM\Column]
    private string $leagueId;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $starters = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $reserve = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $players = [];

    #[ORM\Embedded(class: SleeperRosterSettings::class, columnPrefix: 'rostersettings_')]
    private SleeperRosterSettings $settings;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    private ?array $coOwners = [];

    #[ORM\ManyToOne(targetEntity: SleeperUser::class)]
    #[ORM\JoinColumn(name: 'internal_owner_id', referencedColumnName: 'id')]
    private ?SleeperUser $owner = null;

    #[ORM\ManyToOne(targetEntity: SleeperLeague::class, inversedBy: 'rosters')]
    #[ORM\JoinColumn(name: 'internal_league_id')]
    private ?SleeperLeague $league = null;

    /**
     * @var Collection<int, SleeperDraftPick>
     */
    #[ORM\OneToMany(targetEntity: SleeperDraftPick::class, mappedBy: 'roster')]
    private Collection $draftPicks;

    /**
     * @var Collection<int, SleeperTradedPick>
     */
    #[ORM\OneToMany(targetEntity: SleeperTradedPick::class, mappedBy: 'roster')]
    private Collection $tradedPicks;

    /**
     * @var Collection<int, SleeperMatchup>
     */
    #[ORM\OneToMany(targetEntity: SleeperMatchup::class, mappedBy: 'roster')]
    private Collection $matchups;

    #[ORM\OneToMany(targetEntity: SleeperPlayoffMatchup::class, mappedBy: 'rosterTeam1')]
    private Collection $playoffMatchupsHome;

    #[ORM\OneToMany(targetEntity: SleeperPlayoffMatchup::class, mappedBy: 'rosterTeam2')]
    private Collection $playoffMatchupsAway;

    public function __construct()
    {
        $this->settings = new SleeperRosterSettings();
        $this->draftPicks = new ArrayCollection();
        $this->tradedPicks = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): SleeperRoster
    {
        $this->id = $id;
        return $this;
    }

    public function getRosterId(): int
    {
        return $this->rosterId;
    }

    public function setRosterId(int $rosterId): SleeperRoster
    {
        $this->rosterId = $rosterId;
        return $this;
    }

    public function getOwnerId(): ?string
    {
        return $this->ownerId;
    }

    public function setOwnerId(?string $ownerId): SleeperRoster
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    public function getLeagueId(): string
    {
        return $this->leagueId;
    }

    public function setLeagueId(string $leagueId): SleeperRoster
    {
        $this->leagueId = $leagueId;
        return $this;
    }

    public function getStarters(): ?array
    {
        return $this->starters;
    }

    public function setStarters(?array $starters): SleeperRoster
    {
        $this->starters = $starters;
        return $this;
    }

    public function getReserve(): ?array
    {
        return $this->reserve;
    }

    public function setReserve(?array $reserve): SleeperRoster
    {
        $this->reserve = $reserve;
        return $this;
    }

    public function getPlayers(): ?array
    {
        return $this->players;
    }

    public function setPlayers(?array $players): SleeperRoster
    {
        $this->players = $players;
        return $this;
    }

    public function getSettings(): SleeperRosterSettings
    {
        return $this->settings;
    }

    public function setSettings(SleeperRosterSettings $settings): SleeperRoster
    {
        $this->settings = $settings;
        return $this;
    }

    public function getCoOwners(): ?array
    {
        return $this->coOwners;
    }

    public function setCoOwners(?array $coOwners): SleeperRoster
    {
        $this->coOwners = $coOwners;
        return $this;
    }

    public function buildFindByCriteriaFromDto(SleeperRosterDto $sleeperRosterDto): array
    {
        return [
            'leagueId' => $sleeperRosterDto->getLeagueId(),
            'rosterId' => $sleeperRosterDto->getRosterId()
        ];
    }

    /**
     * @return Collection<int, SleeperDraftPick>
     */
    public function getDraftPicks(): Collection
    {
        return $this->draftPicks;
    }

    /**
     * @param Collection<int, SleeperDraftPick> $draftPicks
     */
    public function setDraftPicks(Collection $draftPicks): SleeperRoster
    {
        $this->draftPicks = $draftPicks;
        return $this;
    }

    public function addDraftPick(SleeperDraftPick $draftPick): SleeperRoster
    {
        if (!$this->draftPicks->contains($draftPick)) {
            $this->draftPicks[] = $draftPick;
            $draftPick->setRoster($this);
        }

        return $this;
    }

    public function removeDraftPick(SleeperDraftPick $draftPick): SleeperRoster
    {
        if ($this->draftPicks->contains($draftPick)) {
            $this->draftPicks->removeElement($draftPick);

            if ($draftPick->getRoster() === $this) {
                $draftPick->setRoster(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?SleeperUser
    {
        return $this->owner;
    }

    public function setOwner(?SleeperUser $owner): SleeperRoster
    {
        $this->owner = $owner;
        return $this;
    }

    public function getLeague(): ?SleeperLeague
    {
        return $this->league;
    }

    public function setLeague(?SleeperLeague $league): SleeperRoster
    {
        $this->league = $league;
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
    public function setTradedPicks(Collection $tradedPicks): SleeperRoster
    {
        $this->tradedPicks = $tradedPicks;
        return $this;
    }

    public function addTradedPick(SleeperTradedPick $pick): SleeperRoster
    {
        if (!$this->draftPicks->contains($pick)) {
            $this->draftPicks[] = $pick;
            $pick->setRoster($this);
        }

        return $this;
    }

    public function removeTradedPick(SleeperTradedPick $pick): SleeperRoster
    {
        if ($this->draftPicks->contains($pick)) {
            $this->draftPicks->removeElement($pick);

            if ($pick->getRoster() === $this) {
                $pick->setRoster(null);
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

    public function setMatchups(Collection $matchups): SleeperRoster
    {
        $this->matchups = $matchups;
        return $this;
    }

    public function addMatchup(SleeperMatchup $matchup): SleeperRoster
    {
        if (!$this->matchups->contains($matchup)) {
            $this->matchups[] = $matchup;
            $matchup->setRoster($this);
        }

        return $this;
    }

    public function removeMatchup(SleeperMatchup $matchup): SleeperRoster
    {
        if ($this->matchups->contains($matchup)) {
            $this->matchups->removeElement($matchup);

            if ($matchup->getRoster() === $this) {
                $matchup->setRoster(null);
            }
        }

        return $this;
    }

    public function getMatchupByWeek(int $week): ?SleeperMatchup
    {
        foreach ($this->matchups as $matchup) {
            if ($matchup->getWeek() === $week) {
                return $matchup;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, SleeperPlayoffMatchup>
     */
    public function getPlayoffMatchupsHome(): Collection
    {
        return $this->playoffMatchupsHome;
    }

    public function setPlayoffMatchupsHome(Collection $playoffMatchups): SleeperRoster
    {
        $this->playoffMatchupsHome = $playoffMatchups;
        return $this;
    }

    public function addPlayoffMatchupHome(SleeperPlayoffMatchup $playoffMatchup): SleeperRoster
    {
        if (!$this->playoffMatchupsHome->contains($playoffMatchup)) {
            $this->playoffMatchupsHome[] = $playoffMatchup;
            $playoffMatchup->setRosterTeam1($this);
        }

        return $this;
    }

    public function removePlayoffMatchupHome(SleeperPlayoffMatchup $playoffMatchup): SleeperRoster
    {
        if ($this->playoffMatchupsHome->contains($playoffMatchup)) {
            $this->playoffMatchupsHome->removeElement($playoffMatchup);

            if ($playoffMatchup->getRosterTeam1() === $this) {
                $playoffMatchup->setRosterTeam1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SleeperPlayoffMatchup>
     */
    public function getPlayoffMatchupsAway(): Collection
    {
        return $this->playoffMatchupsAway;
    }

    public function setPlayoffMatchupsv(Collection $playoffMatchups): SleeperRoster
    {
        $this->playoffMatchupsAway = $playoffMatchups;
        return $this;
    }

    public function addPlayoffMatchupAway(SleeperPlayoffMatchup $playoffMatchup): SleeperRoster
    {
        if (!$this->playoffMatchupsAway->contains($playoffMatchup)) {
            $this->playoffMatchupsAway[] = $playoffMatchup;
            $playoffMatchup->setRosterTeam2($this);
        }

        return $this;
    }

    public function removePlayoffMatchupAway(SleeperPlayoffMatchup $playoffMatchup): SleeperRoster
    {
        if ($this->playoffMatchupsAway->contains($playoffMatchup)) {
            $this->playoffMatchupsAway->removeElement($playoffMatchup);

            if ($playoffMatchup->getRosterTeam2() === $this) {
                $playoffMatchup->setRosterTeam2(null);
            }
        }

        return $this;
    }
}
