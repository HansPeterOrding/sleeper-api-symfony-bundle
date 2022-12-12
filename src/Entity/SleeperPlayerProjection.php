<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use DateTime;
use Doctrine\DBAL\Schema\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjection as SleeperPlayerProjectionDto;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'sasb_player_projection_unique', columns: ['season', 'week', 'player_id'])]
class SleeperPlayerProjection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $week = null;

    #[ORM\Column(nullable: true)]
    private ?string $team = null;

    #[ORM\Embedded(class: SleeperStats::class, columnPrefix: 'stats_')]
    private SleeperStats $stats;

    #[ORM\Column]
    private ?string $sport = null;

    #[ORM\Column]
    private ?string $seasonType = null;

    #[ORM\Column]
    private ?string $season = null;

    #[ORM\Column]
    private ?string $playerId = null;

    #[ORM\ManyToOne(targetEntity: SleeperPlayer::class)]
    #[ORM\JoinColumn(name: 'sleeper_player_id', referencedColumnName: 'id')]
    private SleeperPlayer $player;

    #[ORM\Column(nullable: true)]
    private ?string $opponent = null;

    #[ORM\Column(nullable: true)]
    private ?string $gameId = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $date = null;

    #[ORM\Column(nullable: true)]
    private ?string $company = null;

    #[ORM\Column(nullable: true)]
    private ?string $category = null;

    public function __construct()
    {
        $this->stats = new SleeperStats();
        $this->player = new SleeperPlayer();
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function setWeek(?int $week): SleeperPlayerProjection
    {
        $this->week = $week;
        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(?string $team): SleeperPlayerProjection
    {
        $this->team = $team;
        return $this;
    }

    public function getStats(): SleeperStats
    {
        return $this->stats;
    }

    public function setStats(SleeperStats $stats): SleeperPlayerProjection
    {
        $this->stats = $stats;
        return $this;
    }

    public function getSport(): string
    {
        return $this->sport;
    }

    public function setSport(string $sport): SleeperPlayerProjection
    {
        $this->sport = $sport;
        return $this;
    }

    public function getSeasonType(): string
    {
        return $this->seasonType;
    }

    public function setSeasonType(string $seasonType): SleeperPlayerProjection
    {
        $this->seasonType = $seasonType;
        return $this;
    }

    public function getSeason(): string
    {
        return $this->season;
    }

    public function setSeason(string $season): SleeperPlayerProjection
    {
        $this->season = $season;
        return $this;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function setPlayerId(string $playerId): SleeperPlayerProjection
    {
        $this->playerId = $playerId;
        return $this;
    }

    public function getPlayer(): SleeperPlayer
    {
        return $this->player;
    }

    public function setPlayer(SleeperPlayer $player): SleeperPlayerProjection
    {
        $this->player = $player;
        return $this;
    }

    public function getOpponent(): ?string
    {
        return $this->opponent;
    }

    public function setOpponent(?string $opponent): SleeperPlayerProjection
    {
        $this->opponent = $opponent;
        return $this;
    }

    public function getGameId(): ?string
    {
        return $this->gameId;
    }

    public function setGameId(?string $gameId): SleeperPlayerProjection
    {
        $this->gameId = $gameId;
        return $this;
    }

    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    public function setDate(?DateTime $date): SleeperPlayerProjection
    {
        $this->date = $date;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): SleeperPlayerProjection
    {
        $this->company = $company;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): SleeperPlayerProjection
    {
        $this->category = $category;
        return $this;
    }

    public function buildFindByCriteriaFromDto(SleeperPlayerProjectionDto $sleeperPlayerProjectionDto): array
    {
        return [
            'playerId' => $sleeperPlayerProjectionDto->getPlayerId(),
            'season' => $sleeperPlayerProjectionDto->getSeason(),
            'week' => $sleeperPlayerProjectionDto->getWeek()
        ];
    }
}
