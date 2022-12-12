<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto as Dto;

#[ORM\Entity]
#[ORM\UniqueConstraint(
    name: 'sasb_sleeper_matchup_unique',
    columns: ['league_id', 'week', 'roster_id']
)]
class SleeperMatchup
{
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

    #[ORM\Column]
    private ?int $matchupId = null;

    #[ORM\Column]
    private ?float $points = null;

    #[ORM\Column(nullable: true)]
    private ?float $customPoints;

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

    public function buildFindByCriteriaFromDto(string $leagueId, int $week, Dto\SleeperMatchup $sleeperMatchupDto): array
    {
        return [
            'leagueId' => $leagueId,
            'week' => $week,
            'rosterId' => $sleeperMatchupDto->getRosterId()
        ];
    }
}
