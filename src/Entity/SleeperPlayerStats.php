<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats as SleeperPlayerStatsDto;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'sasb_player_stats_unique', columns: ['season', 'week', 'player_id'])]
class SleeperPlayerStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $week;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $team;

    #[ORM\Embedded(class: SleeperStats::class, columnPrefix: 'stats_')]
    private SleeperStats $stats;

    #[ORM\Column]
    private string $sport;

    #[ORM\Column]
    private string $seasonType;

    #[ORM\Column]
    private string $season;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $playerId;

    #[ORM\ManyToOne(targetEntity: SleeperPlayer::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'sleeper_player_id', referencedColumnName: 'id')]
    private ?SleeperPlayer $player;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $opponent;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $gameId;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $date;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $company;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $category;

    public function __construct()
    {
        $this->stats = new SleeperStats();
        $this->player = new SleeperPlayer();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): SleeperPlayerStats
    {
        $this->id = $id;
        return $this;
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(?int $week): SleeperPlayerStats
    {
        $this->week = $week;
        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(?string $team): SleeperPlayerStats
    {
        $this->team = $team;
        return $this;
    }

    public function getStats(): SleeperStats
    {
        return $this->stats;
    }

    public function setStats(SleeperStats $stats): SleeperPlayerStats
    {
        $this->stats = $stats;
        return $this;
    }

    public function getSport(): string
    {
        return $this->sport;
    }

    public function setSport(string $sport): SleeperPlayerStats
    {
        $this->sport = $sport;
        return $this;
    }

    public function getSeasonType(): string
    {
        return $this->seasonType;
    }

    public function setSeasonType(string $seasonType): SleeperPlayerStats
    {
        $this->seasonType = $seasonType;
        return $this;
    }

    public function getSeason(): string
    {
        return $this->season;
    }

    public function setSeason(string $season): SleeperPlayerStats
    {
        $this->season = $season;
        return $this;
    }

    public function getPlayerId(): ?string
    {
        return $this->playerId;
    }

    public function setPlayerId(?string $playerId): SleeperPlayerStats
    {
        $this->playerId = $playerId;
        return $this;
    }

    public function getPlayer(): ?SleeperPlayer
    {
        return $this->player;
    }

    public function setPlayer(?SleeperPlayer $player): SleeperPlayerStats
    {
        $this->player = $player;
        return $this;
    }

    public function getOpponent(): ?string
    {
        return $this->opponent;
    }

    public function setOpponent(?string $opponent): SleeperPlayerStats
    {
        $this->opponent = $opponent;
        return $this;
    }

    public function getGameId(): ?string
    {
        return $this->gameId;
    }

    public function setGameId(?string $gameId): SleeperPlayerStats
    {
        $this->gameId = $gameId;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): SleeperPlayerStats
    {
        $this->date = $date;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): SleeperPlayerStats
    {
        $this->company = $company;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): SleeperPlayerStats
    {
        $this->category = $category;
        return $this;
    }

    public function buildFindByCriteriaFromDto(SleeperPlayerStatsDto $sleeperPlayerStatsDto): array
    {
        return [
            'playerId' => $sleeperPlayerStatsDto->getPlayerId(),
            'season' => $sleeperPlayerStatsDto->getSeason(),
            'week' => $sleeperPlayerStatsDto->getWeek()
        ];
    }
}
