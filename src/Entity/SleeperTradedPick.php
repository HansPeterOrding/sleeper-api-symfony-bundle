<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTradedPick as SleeperTradedPickDto;

#[Entity]
class SleeperTradedPick
{
    #[Id, GeneratedValue, Column]
    private int $id;

    #[Column]
    private string $season;

    #[Column]
    private int $round;

    #[Column]
    private int $rosterId;

    #[Column]
    private int $previousOwnerId;

    #[Column]
    private int $ownerId;

    #[Column(nullable: false)]
    private ?string $draftId = null;

    #[Column]
    private ?string $leagueId = null;

    #[ManyToOne(targetEntity: SleeperRoster::class, inversedBy: 'tradedPicks')]
    #[JoinColumn(name: 'internal_roster_id', referencedColumnName: 'id')]
    private ?SleeperRoster $roster = null;

    #[ManyToOne(targetEntity: SleeperUser::class, inversedBy: 'soldPicks')]
    #[JoinColumn(name: 'internal_previous_owner_id')]
    private ?SleeperUser $previousOwner = null;

    #[ManyToOne(targetEntity: SleeperUser::class, inversedBy: 'acquiredPicks')]
    #[JoinColumn(name: 'internal_owner_id')]
    private ?SleeperUser $owner = null;

    #[ManyToOne(targetEntity: SleeperDraft::class, inversedBy: 'tradedPicks')]
    #[JoinColumn(name: 'internal_draft_id')]
    private ?SleeperDraft $draft = null;

    #[ManyToOne(targetEntity: SleeperLeague::class, inversedBy: 'tradedPicks')]
    #[JoinColumn(name: 'internal_league_id')]
    private ?SleeperLeague $league = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): SleeperTradedPick
    {
        $this->id = $id;
        return $this;
    }

    public function getSeason(): string
    {
        return $this->season;
    }

    public function setSeason(string $season): SleeperTradedPick
    {
        $this->season = $season;
        return $this;
    }

    public function getRound(): int
    {
        return $this->round;
    }

    public function setRound(int $round): SleeperTradedPick
    {
        $this->round = $round;
        return $this;
    }

    public function getRosterId(): int
    {
        return $this->rosterId;
    }

    public function setRosterId(int $rosterId): SleeperTradedPick
    {
        $this->rosterId = $rosterId;
        return $this;
    }

    public function getPreviousOwnerId(): int
    {
        return $this->previousOwnerId;
    }

    public function setPreviousOwnerId(int $previousOwnerId): SleeperTradedPick
    {
        $this->previousOwnerId = $previousOwnerId;
        return $this;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function setOwnerId(int $ownerId): SleeperTradedPick
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    public function getDraftId(): ?string
    {
        return $this->draftId;
    }

    public function setDraftId(?string $draftId): SleeperTradedPick
    {
        $this->draftId = $draftId;
        return $this;
    }

    public function getLeagueId(): ?string
    {
        return $this->leagueId;
    }

    public function setLeagueId(?string $leagueId): SleeperTradedPick
    {
        $this->leagueId = $leagueId;
        return $this;
    }

    public function getRoster(): ?SleeperRoster
    {
        return $this->roster;
    }

    public function setRoster(?SleeperRoster $roster): SleeperTradedPick
    {
        $this->roster = $roster;
        return $this;
    }

    public function getPreviousOwner(): ?SleeperUser
    {
        return $this->previousOwner;
    }

    public function setPreviousOwner(?SleeperUser $previousOwner): SleeperTradedPick
    {
        $this->previousOwner = $previousOwner;
        return $this;
    }

    public function getOwner(): ?SleeperUser
    {
        return $this->owner;
    }

    public function setOwner(?SleeperUser $owner): SleeperTradedPick
    {
        $this->owner = $owner;
        return $this;
    }

    public function getDraft(): ?SleeperDraft
    {
        return $this->draft;
    }

    public function setDraft(?SleeperDraft $draft): SleeperTradedPick
    {
        $this->draft = $draft;
        return $this;
    }

    public function getLeague(): ?SleeperLeague
    {
        return $this->league;
    }

    public function setLeague(?SleeperLeague $league): SleeperTradedPick
    {
        $this->league = $league;
        return $this;
    }

    public function buildFindByCriteriaFromDto(string $leagueId, string $draftId, SleeperTradedPickDto $sleeperTradedPickDto): array
    {
        return [
            'leagueId' => $leagueId, 
            'draftId' => $draftId,
            'season' => $sleeperTradedPickDto->getSeason(),
            'round' => $sleeperTradedPickDto->getRound(),
            'rosterId' => $sleeperTradedPickDto->getRosterId()
        ];
    }
}
