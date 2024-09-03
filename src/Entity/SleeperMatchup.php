<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto as Dto;

#[ORM\Entity]
#[ORM\UniqueConstraint(
    name: 'sasb_sleeper_matchup_unique',
    columns: ['league_id', 'week', 'roster_id']
)]
class SleeperMatchup {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $leagueId = null;

    #[ORM\Column]
    private ?int $week = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $startersPoints = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $playersPoints = [];

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $starters = [];

    #[ORM\Column]
    private ?int $rosterId = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $players = [];

    #[ORM\Column(nullable: true)]
    private ?int $matchupId = null;

    #[ORM\Column]
    private ?float $points = null;

    #[ORM\Column(nullable: true)]
    private ?float $customPoints;

    #[ORM\ManyToOne(targetEntity: SleeperLeague::class, inversedBy: 'matchups')]
    #[ORM\JoinColumn(name: 'internal_league_id')]
    private ?SleeperLeague $league = null;

    #[ORM\JoinTable(name: 'matchup_starters')]
    #[ORM\JoinColumn(name: 'matchup_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'player_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: SleeperPlayer::class)]
    private Collection $sleeperStarterPlayers;

    /**
     * @var array<int, SleeperPlayer>
     */
    #[ORM\JoinTable(name: 'matchup_players')]
    #[ORM\JoinColumn(name: 'matchup_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'player_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: SleeperPlayer::class)]
    private Collection $sleeperPlayers;

    #[ORM\ManyToOne(targetEntity: SleeperRoster::class, inversedBy: 'matchups')]
    #[ORM\JoinColumn(name: 'internal_roster_id')]
    private ?SleeperRoster $roster = null;

    public function __construct()
    {
        $this->sleeperStarterPlayers = new ArrayCollection();
        $this->sleeperPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): SleeperMatchup
    {
        $this->id = $id;
        return $this;
    }

    public function getLeagueId(): ?string
    {
        return $this->leagueId;
    }

    public function setLeagueId(?string $leagueId): SleeperMatchup
    {
        $this->leagueId = $leagueId;
        return $this;
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(?int $week): SleeperMatchup
    {
        $this->week = $week;
        return $this;
    }

    public function getStartersPoints(): ?array
    {
        return $this->startersPoints;
    }

    public function setStartersPoints(?array $startersPoints): SleeperMatchup
    {
        $this->startersPoints = $startersPoints;
        return $this;
    }

    public function getPlayersPoints(): ?array
    {
        return $this->playersPoints;
    }

    public function setPlayersPoints(?array $playersPoints): SleeperMatchup
    {
        $this->playersPoints = $playersPoints;
        return $this;
    }

    public function getStarters(): ?array
    {
        return $this->starters;
    }

    public function setStarters(?array $starters): SleeperMatchup
    {
        $this->starters = $starters;
        return $this;
    }

    public function getRosterId(): ?int
    {
        return $this->rosterId;
    }

    public function setRosterId(?int $rosterId): SleeperMatchup
    {
        $this->rosterId = $rosterId;
        return $this;
    }

    public function getPlayers(): ?array
    {
        return $this->players;
    }

    public function setPlayers(?array $players): SleeperMatchup
    {
        $this->players = $players;
        return $this;
    }

    public function getMatchupId(): ?int
    {
        return $this->matchupId;
    }

    public function setMatchupId(?int $matchupId): SleeperMatchup
    {
        $this->matchupId = $matchupId;
        return $this;
    }

    public function getPoints(): ?float
    {
        return $this->points;
    }

    public function setPoints(?float $points): SleeperMatchup
    {
        $this->points = $points;
        return $this;
    }

    public function getCustomPoints(): ?float
    {
        return $this->customPoints;
    }

    public function setCustomPoints(?float $customPoints): SleeperMatchup
    {
        $this->customPoints = $customPoints;
        return $this;
    }

    public function getEffectivePoints(): ?float
    {
        if ($this->customPoints) {
            return $this->customPoints;
        }
        return $this->points;
    }

    public function buildFindByCriteriaFromDto(string $leagueId, int $week, Dto\SleeperMatchup $sleeperMatchupDto): array
    {
        return [
            'leagueId' => $leagueId,
            'week' => $week,
            'rosterId' => $sleeperMatchupDto->getRosterId()
        ];
    }

    public function getLeague(): ?SleeperLeague
    {
        return $this->league;
    }

    public function setLeague(?SleeperLeague $league): SleeperMatchup
    {
        $this->league = $league;
        return $this;
    }

    public function getSleeperStarterPlayers(): Collection
    {
        return $this->sleeperStarterPlayers;
    }

    public function setSleeperStarterPlayers(Collection $sleeperStarterPlayers): SleeperMatchup
    {
        $this->sleeperStarterPlayers = $sleeperStarterPlayers;
        return $this;
    }

    public function addSleeperStarterPlayer(SleeperPlayer $player): SleeperMatchup
    {
        if (!$this->sleeperStarterPlayers->contains($player)) {
            $this->sleeperStarterPlayers[] = $player;
        }

        return $this;
    }

    public function removeSleeperStarterPlayer(SleeperPlayer $player): SleeperMatchup
    {
        if ($this->sleeperStarterPlayers->contains($player)) {
            $this->sleeperStarterPlayers->removeElement($player);
        }

        return $this;
    }

    public function getSleeperStarterPlayerById(string $id): ?SleeperPlayer
    {
        foreach ($this->sleeperStarterPlayers as $sleeperPlayer) {
            if ($sleeperPlayer->getPlayerId() === $id) {
                return $sleeperPlayer;
            }
        }
    }

    public function getSleeperPlayers(): Collection
    {
        return $this->sleeperPlayers;
    }

    public function setSleeperPlayers(Collection $sleeperPlayers): SleeperMatchup
    {
        $this->sleeperPlayers = $sleeperPlayers;
        return $this;
    }

    public function addSleeperPlayer(SleeperPlayer $player): SleeperMatchup
    {
        if (!$this->sleeperPlayers->contains($player)) {
            $this->sleeperPlayers[] = $player;
        }

        return $this;
    }

    public function removeSleeperPlayer(SleeperPlayer $player): SleeperMatchup
    {
        if ($this->sleeperPlayers->contains($player)) {
            $this->sleeperPlayers->removeElement($player);
        }

        return $this;
    }

    public function getSleeperPlayerById(string $id): ?SleeperPlayer
    {
        foreach ($this->sleeperPlayers as $sleeperPlayer) {
            if ($sleeperPlayer->getPlayerId() === $id) {
                return $sleeperPlayer;
            }
        }
    }

    public function getRoster(): ?SleeperRoster
    {
        return $this->roster;
    }

    public function setRoster(?SleeperRoster $roster): SleeperMatchup
    {
        $this->roster = $roster;
        return $this;
    }
}
