<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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

    public function __construct()
    {
        $this->settings = new SleeperRosterSettings();
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

    public function buildFindByCriteriaFromDto(SleeperRosterDto $sleeperRosterDto): array
    {
        return [
            'leagueId' => $sleeperRosterDto->getLeagueId(),
            'rosterId' => $sleeperRosterDto->getRosterId()
        ];
    }
}
