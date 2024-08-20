<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperDraft as SleeperDraftDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\DraftStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\DraftPositionEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\DraftTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\FantasyPositionEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SeasonTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SportEnum;

#[ORM\Entity]
class SleeperDraft
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private int $id;

    #[ORM\Column]
    private DraftTypeEnum $type;

    #[ORM\Column]
    private DraftStatusEnum $status;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $startTime;

    #[ORM\Column]
    private SportEnum $sport;

    #[ORM\Column]
    private array $slotToRosterId = [];

    #[ORM\Embedded(class: SleeperDraftSettings::class, columnPrefix: 'settings_')]
    private SleeperDraftSettings $settings;

    #[ORM\Column]
    private SeasonTypeEnum $seasonType;

    #[ORM\Column]
    private ?string $season = null;

    #[ORM\Embedded(class: SleeperDraftMetadata::class, columnPrefix: 'metadata_')]
    private SleeperDraftMetadata $metadata;

    #[ORM\Column(nullable: true)]
    private ?string $leagueId = null;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $lastPicked = null;

    #[ORM\Column(type: 'bigint')]
    private ?int $lastMessageTime = null;

    #[ORM\Column]
    private ?string $lastMessageId = null;

    #[ORM\Column(nullable: true)]
    private ?array $draftOrder = null;

    #[ORM\Column]
    private ?string $draftId = null;

    #[ORM\Column]
    private ?array $creators = null;

    #[ORM\Column(type: 'bigint')]
    private ?int $created = null;

    /**
     * @var Collection<int, SleeperDraftPick>
     */
    #[ORM\OneToMany(mappedBy: 'draft', targetEntity: SleeperDraftPick::class)]
    private Collection $draftPicks;

    #[ORM\OneToOne(targetEntity: SleeperLeague::class, inversedBy: 'draft')]
    #[ORM\JoinColumn(name: 'internal_league_id')]
    private ?SleeperLeague $league = null;

    /**
     * @var Collection<int, SleeperTradedPick>
     */
    #[ORM\OneToMany(targetEntity: SleeperTradedPick::class, mappedBy: 'draft')]
    private Collection $tradedPicks;

    public function __construct()
    {
        $this->settings = new SleeperDraftSettings();
        $this->metadata = new SleeperDraftMetadata();
        $this->draftPicks = new ArrayCollection();
        $this->tradedPicks = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getType(): DraftTypeEnum
    {
        return $this->type;
    }

    public function setType(DraftTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getStatus(): DraftStatusEnum
    {
        return $this->status;
    }

    public function setStatus(DraftStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getStartTime(): ?int
    {
        return $this->startTime;
    }

    public function setStartTime(?int $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getSport(): SportEnum
    {
        return $this->sport;
    }

    public function setSport(SportEnum $sport): void
    {
        $this->sport = $sport;
    }

    public function getSlotToRosterId(): array
    {
        return $this->slotToRosterId;
    }

    public function setSlotToRosterId(array $slotToRosterId): void
    {
        $this->slotToRosterId = $slotToRosterId;
    }

    public function getSettings(): SleeperDraftSettings
    {
        return $this->settings;
    }

    public function setSettings(SleeperDraftSettings $settings): void
    {
        $this->settings = $settings;
    }

    public function getSeasonType(): SeasonTypeEnum
    {
        return $this->seasonType;
    }

    public function setSeasonType(SeasonTypeEnum $seasonType): void
    {
        $this->seasonType = $seasonType;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(?string $season): void
    {
        $this->season = $season;
    }

    public function getMetadata(): SleeperDraftMetadata
    {
        return $this->metadata;
    }

    public function setMetadata(SleeperDraftMetadata $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getLeagueId(): ?string
    {
        return $this->leagueId;
    }

    public function setLeagueId(?string $leagueId): void
    {
        $this->leagueId = $leagueId;
    }

    public function getLastPicked(): ?int
    {
        return $this->lastPicked;
    }

    public function setLastPicked(?int $lastPicked): void
    {
        $this->lastPicked = $lastPicked;
    }

    public function getLastMessageTime(): ?int
    {
        return $this->lastMessageTime;
    }

    public function setLastMessageTime(?int $lastMessageTime): void
    {
        $this->lastMessageTime = $lastMessageTime;
    }

    public function getLastMessageId(): ?string
    {
        return $this->lastMessageId;
    }

    public function setLastMessageId(?string $lastMessageId): void
    {
        $this->lastMessageId = $lastMessageId;
    }

    public function getDraftOrder(): ?array
    {
        return $this->draftOrder;
    }

    public function setDraftOrder(?array $draftOrder): void
    {
        $this->draftOrder = $draftOrder;
    }

    public function getDraftId(): ?string
    {
        return $this->draftId;
    }

    public function setDraftId(?string $draftId): void
    {
        $this->draftId = $draftId;
    }

    public function getCreators(): ?array
    {
        return $this->creators;
    }

    public function setCreators(?array $creators): void
    {
        $this->creators = $creators;
    }

    public function getCreated(): ?int
    {
        return $this->created;
    }

    public function setCreated(?int $created): void
    {
        $this->created = $created;
    }

    /**
     * @return SleeperDraftPick[]
     */
    public function getDraftPicks(): Collection
    {
        return $this->draftPicks;
    }

    public function setDraftPicks(Collection $draftPicks): SleeperDraft
    {
        $this->draftPicks = $draftPicks;
        return $this;
    }

    public function addDraftPick(SleeperDraftPick $draftPick): SleeperDraft
    {
        if (!$this->draftPicks->contains($draftPick)) {
            $this->draftPicks[] = $draftPick;
            $draftPick->setDraft($this);
        }

        return $this;
    }

    public function removeDraftPick(SleeperDraftPick $draftPick): SleeperDraft
    {
        if ($this->draftPicks->contains($draftPick)) {
            $this->draftPicks->removeElement($draftPick);

            if ($draftPick->getDraft() === $this) {
                $draftPick->setDraft(null);
            }
        }

        return $this;
    }

    public function getLeague(): ?SleeperLeague
    {
        return $this->league;
    }

    public function setLeague(?SleeperLeague $league): SleeperDraft
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

    public function buildFindByCriteriaFromDto(SleeperDraftDto $sleeperDraftDto): array
    {
        return [
            'draftId' => $sleeperDraftDto->getDraftId()
        ];
    }

    public function getDraftPickByRoundAndDraftSlot(int $round, int $draftSlot): ?SleeperDraftPick
    {
        foreach($this->draftPicks as $draftPick) {
            if($draftPick->getRound() === $round && $draftPick->getDraftSlot() === $draftSlot) {
                return $draftPick;
            }
        }

        return null;
    }

    public function getAuctionDraftPickByRoundAndDraftSlot(int $round, int $draftSlot, array $rosterPositions)
    {
        $draftPicks = clone($this->draftPicks);
        $sortedDraftPicks = [];

        foreach($rosterPositions as $rosterPosition) {
            foreach($draftPicks as $idx => $draftPick) {
                if($draftPick->getDraftSlot() === $draftSlot) {
                    if(in_array($draftPick->getMetadata()->getPosition(), $this->getDraftSlotPositionToPositionMapping()[$rosterPosition])) {
                        $sortedDraftPicks[] = $draftPick;
                        unset($draftPicks[$idx]);
                        continue 2;
                    }
                }
            }
        }

        return $sortedDraftPicks[$round - 1];
    }

    public function getDraftPickByPickNumber(int $pickNumber): ?SleeperDraftPick
    {
        foreach($this->draftPicks as $draftPick) {
            if($draftPick->getPickNo() === $pickNumber) {
                return $draftPick;
            }
        }

        return null;
    }

    public function getTradedPick(int $round, int $draftSlot): ?SleeperTradedPick
    {
        $rosterId = $this->getSlotToRosterId()[$draftSlot];

        foreach($this->tradedPicks as $tradedPick) {
            if($tradedPick->getRound() === $round) {
                if($tradedPick->getRosterId() === $rosterId) {
                    return $tradedPick;
                }
            }
        }

        return null;
    }

    public function getRosterByRosterId(int $rosterId): ?SleeperRoster
    {
        if($this->getLeague()) {
            foreach($this->getLeague()->getRosters() as $roster) {
                if($roster->getRosterId() === $rosterId) {
                    return $roster;
                }
            }
        }

        return null;
    }

    private function getDraftSlotPositionToPositionMapping(): array
    {
        return [
            DraftPositionEnum::QB->value => [FantasyPositionEnum::QB],
            DraftPositionEnum::RB->value => [FantasyPositionEnum::RB],
            DraftPositionEnum::WR->value => [FantasyPositionEnum::WR],
            DraftPositionEnum::TE->value => [FantasyPositionEnum::TE],
            DraftPositionEnum::DL->value => [FantasyPositionEnum::DL],
            DraftPositionEnum::LB->value => [FantasyPositionEnum::LB],
            DraftPositionEnum::DB->value => [FantasyPositionEnum::DB],
            DraftPositionEnum::FLEX->value => [FantasyPositionEnum::RB, FantasyPositionEnum::WR, FantasyPositionEnum::TE],
            DraftPositionEnum::SUPER_FLEX->value => [FantasyPositionEnum::QB, FantasyPositionEnum::RB, FantasyPositionEnum::WR, FantasyPositionEnum::TE],
            DraftPositionEnum::REC_FLEX->value => [FantasyPositionEnum::WR, FantasyPositionEnum::TE],
            DraftPositionEnum::IDP_FLEX->value => [FantasyPositionEnum::DL, FantasyPositionEnum::LB, FantasyPositionEnum::DB],
            DraftPositionEnum::BN->value => [FantasyPositionEnum::QB, FantasyPositionEnum::RB, FantasyPositionEnum::WR, FantasyPositionEnum::TE, FantasyPositionEnum::DL, FantasyPositionEnum::LB, FantasyPositionEnum::DB],
        ];
    }
}
