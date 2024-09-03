<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftPick as SleeperDraftPickDto;

#[ORM\Entity]
class SleeperDraftPick {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $round = null;

    #[ORM\Column(nullable: true)]
    private ?int $rosterId = null;

    #[ORM\Column]
    private ?string $playerId;

    #[ORM\Column]
    private ?string $pickedBy;

    #[ORM\Column]
    private int $pickNo;

    #[ORM\Embedded(class: SleeperDraftPickMetadata::class, columnPrefix: 'metadata_')]
    private SleeperDraftPickMetadata $metadata;

    #[ORM\Column(nullable: true)]
    private ?bool $isKeeper = null;

    #[ORM\Column]
    private ?int $draftSlot = null;

    #[ORM\Column]
    private string $draftId;

    #[ORM\ManyToOne(targetEntity: SleeperDraft::class, inversedBy: 'draftPicks')]
    #[ORM\JoinColumn(name: 'internal_draft_id')]
    private ?SleeperDraft $draft = null;

    #[ORM\ManyToOne(targetEntity: SleeperPlayer::class, inversedBy: 'draftPicks')]
    #[ORM\JoinColumn(name: 'internal_player_id')]
    private ?SleeperPlayer $player = null;

    #[ORM\ManyToOne(targetEntity: SleeperRoster::class, inversedBy: 'draftPicks')]
    #[ORM\JoinColumn(name: 'internal_roster_id')]
    private ?SleeperRoster $roster = null;

    #[ORM\ManyToOne(targetEntity: SleeperUser::class, inversedBy: 'draftPicks')]
    #[ORM\JoinColumn(name: 'internal_user_id')]
    private ?SleeperUser $user = null;

    public function __construct()
    {
        $this->metadata = new SleeperDraftPickMetadata();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): SleeperDraftPick
    {
        $this->id = $id;
        return $this;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(?int $round): SleeperDraftPick
    {
        $this->round = $round;
        return $this;
    }

    public function getRosterId(): ?int
    {
        return $this->rosterId;
    }

    public function setRosterId(?int $rosterId): SleeperDraftPick
    {
        $this->rosterId = $rosterId;
        return $this;
    }

    public function getPlayerId(): ?string
    {
        return $this->playerId;
    }

    public function setPlayerId(?string $playerId): SleeperDraftPick
    {
        $this->playerId = $playerId;
        return $this;
    }

    public function getPickedBy(): ?string
    {
        return $this->pickedBy;
    }

    public function setPickedBy(?string $pickedBy): SleeperDraftPick
    {
        $this->pickedBy = $pickedBy;
        return $this;
    }

    public function getPickNo(): int
    {
        return $this->pickNo;
    }

    public function setPickNo(int $pickNo): SleeperDraftPick
    {
        $this->pickNo = $pickNo;
        return $this;
    }

    public function getMetadata(): SleeperDraftPickMetadata
    {
        return $this->metadata;
    }

    public function setMetadata(SleeperDraftPickMetadata $metadata): SleeperDraftPick
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getIsKeeper(): ?bool
    {
        return $this->isKeeper;
    }

    public function setIsKeeper(?bool $isKeeper): SleeperDraftPick
    {
        $this->isKeeper = $isKeeper;
        return $this;
    }

    public function getDraftSlot(): ?int
    {
        return $this->draftSlot;
    }

    public function setDraftSlot(?int $draftSlot): SleeperDraftPick
    {
        $this->draftSlot = $draftSlot;
        return $this;
    }

    public function getDraftId(): string
    {
        return $this->draftId;
    }

    public function setDraftId(string $draftId): SleeperDraftPick
    {
        $this->draftId = $draftId;
        return $this;
    }

    public function getDraft(): ?SleeperDraft
    {
        return $this->draft;
    }

    public function setDraft(?SleeperDraft $draft): SleeperDraftPick
    {
        $this->draft = $draft;
        return $this;
    }

    public function getPlayer(): ?SleeperPlayer
    {
        return $this->player;
    }

    public function setPlayer(?SleeperPlayer $player): SleeperDraftPick
    {
        $this->player = $player;
        return $this;
    }

    public function getRoster(): ?SleeperRoster
    {
        return $this->roster;
    }

    public function setRoster(?SleeperRoster $roster): SleeperDraftPick
    {
        $this->roster = $roster;
        return $this;
    }

    public function getUser(): ?SleeperUser
    {
        return $this->user;
    }

    public function setUser(?SleeperUser $user): SleeperDraftPick
    {
        $this->user = $user;
        return $this;
    }

    public function buildFindByCriteriaFromDto(SleeperDraftPickDto $sleeperDraftPickDto): array
    {
        return [
            'draftId' => $sleeperDraftPickDto->getDraftId(),
            'pickNo' => $sleeperDraftPickDto->getPickNo()
        ];
    }
}
