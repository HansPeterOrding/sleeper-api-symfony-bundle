<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTransaction as SleeperTransactionDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\TransactionStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\TransactionTypeEnum;

#[ORM\Entity]
class SleeperTransaction {
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private int $id;

    #[ORM\Embedded(class: SleeperTransactionWaiverBudget::class, columnPrefix: 'waiver_budget_')]
    private ?SleeperTransactionWaiverBudget $waiverBudget = null;

    #[ORM\Column]
    private ?TransactionTypeEnum $type = null;

    #[ORM\Column]
    private string $transactionId;

    #[ORM\Column(type: 'bigint')]
    private int $statusUpdated;

    #[ORM\Column]
    private TransactionStatusEnum $status;

    #[ORM\Embedded(class: SleeperTransactionSettings::class, columnPrefix: 'settings_')]
    private ?SleeperTransactionSettings $settings = null;

    #[ORM\Column(type: 'json')]
    private ?array $rosterIds = [];

    #[ORM\Embedded(class: SleeperTransactionMetadata::class, columnPrefix: 'metadata_')]
    private ?SleeperTransactionMetadata $metadata = null;

    #[ORM\Column]
    private int $leg;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $drops;

    #[ORM\Column(type: 'json')]
    private ?array $draftPicks;

    #[ORM\Column]
    private string $creator;

    #[ORM\ManyToOne(targetEntity: SleeperUser::class, inversedBy: 'transactions')]
    #[ORM\JoinColumn(name: 'internal_sleeper_user_id')]
    private ?SleeperUser $creatorUser = null;

    #[ORM\Column(type: 'bigint')]
    private int $created;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $consenterIds;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $adds;

    #[ORM\ManyToOne(targetEntity: SleeperLeague::class, inversedBy: 'transactions')]
    #[ORM\JoinColumn(name: 'internal_league_id')]
    private ?SleeperLeague $league = null;

    /**
     * @var Collection<int, SleeperRoster>
     */
    #[ORM\ManyToMany(targetEntity: SleeperRoster::class)]
    private Collection $rosters;

    /**
     * @var Collection<int, SleeperPlayer>
     */
    #[ORM\ManyToMany(targetEntity: SleeperPlayer::class)]
    #[ORM\JoinTable(name: 'sleeper_transactions_dropped_players')]
    #[ORM\JoinColumn(name: 'transaction_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'player_id', referencedColumnName: 'id')]
    private Collection $droppedPlayers;

    /**
     * @var Collection<int, SleeperPlayer>
     */
    #[ORM\ManyToMany(targetEntity: SleeperPlayer::class)]
    #[ORM\JoinTable(name: 'sleeper_transactions_added_players')]
    #[ORM\JoinColumn(name: 'transaction_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'player_id', referencedColumnName: 'id')]
    private Collection $addedPlayers;

    /**
     * @var Collection<int, SleeperRoster>
     */
    #[ORM\ManyToMany(targetEntity: SleeperRoster::class)]
    #[ORM\JoinTable(name: 'sleeper_transactions_consenter_users')]
    #[ORM\JoinColumn(name: 'transaction_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'roster_id', referencedColumnName: 'id')]
    private Collection $consenterRosters;

    public function __construct()
    {
        $this->waiverBudget = new SleeperTransactionWaiverBudget();
        $this->metadata = new SleeperTransactionMetadata();
        $this->settings = new SleeperTransactionSettings();
        $this->rosters = new ArrayCollection();
        $this->droppedPlayers = new ArrayCollection();
        $this->addedPlayers = new ArrayCollection();
        $this->consenterRosters = new ArrayCollection();
    }

    public function getCreator(): string
    {
        return $this->creator;
    }

    public function setCreator(string $creator): SleeperTransaction
    {
        $this->creator = $creator;
        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): SleeperTransaction
    {
        $this->id = $id;
        return $this;
    }

    public function getWaiverBudget(): ?SleeperTransactionWaiverBudget
    {
        return $this->waiverBudget;
    }

    public function setWaiverBudget(?SleeperTransactionWaiverBudget $waiverBudget): SleeperTransaction
    {
        $this->waiverBudget = $waiverBudget;
        return $this;
    }

    public function getType(): ?TransactionTypeEnum
    {
        return $this->type;
    }

    public function setType(?TransactionTypeEnum $type): SleeperTransaction
    {
        $this->type = $type;
        return $this;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function setTransactionId(string $transactionId): SleeperTransaction
    {
        $this->transactionId = $transactionId;
        return $this;
    }

    public function getStatusUpdated(): int
    {
        return $this->statusUpdated;
    }

    public function setStatusUpdated(int $statusUpdated): SleeperTransaction
    {
        $this->statusUpdated = $statusUpdated;
        return $this;
    }

    public function getStatus(): TransactionStatusEnum
    {
        return $this->status;
    }

    public function setStatus(TransactionStatusEnum $status): SleeperTransaction
    {
        $this->status = $status;
        return $this;
    }

    public function getSettings(): ?SleeperTransactionSettings
    {
        return $this->settings;
    }

    public function setSettings(?SleeperTransactionSettings $settings): SleeperTransaction
    {
        $this->settings = $settings;
        return $this;
    }

    public function getRosterIds(): ?array
    {
        return $this->rosterIds;
    }

    public function setRosterIds(?array $rosterIds): SleeperTransaction
    {
        $this->rosterIds = $rosterIds;
        return $this;
    }

    public function getMetadata(): ?SleeperTransactionMetadata
    {
        return $this->metadata;
    }

    public function setMetadata(?SleeperTransactionMetadata $metadata): SleeperTransaction
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getLeg(): int
    {
        return $this->leg;
    }

    public function setLeg(int $leg): SleeperTransaction
    {
        $this->leg = $leg;
        return $this;
    }

    public function getDrops(): ?array
    {
        return $this->drops;
    }

    public function setDrops(?array $drops): SleeperTransaction
    {
        $this->drops = $drops;
        return $this;
    }

    public function getDraftPicks(): ?array
    {
        return $this->draftPicks;
    }

    public function setDraftPicks(?array $draftPicks): SleeperTransaction
    {
        $this->draftPicks = $draftPicks;
        return $this;
    }

    public function getCreatorUser(): ?SleeperUser
    {
        return $this->creatorUser;
    }

    public function setCreatorUser(?SleeperUser $creatorUser): SleeperTransaction
    {
        $this->creatorUser = $creatorUser;
        return $this;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): SleeperTransaction
    {
        $this->created = $created;
        return $this;
    }

    public function getConsenterIds(): ?array
    {
        return $this->consenterIds;
    }

    public function setConsenterIds(?array $consenterIds): SleeperTransaction
    {
        $this->consenterIds = $consenterIds;
        return $this;
    }

    public function getAdds(): ?array
    {
        return $this->adds;
    }

    public function setAdds(?array $adds): SleeperTransaction
    {
        $this->adds = $adds;
        return $this;
    }

    public function getLeague(): ?SleeperLeague
    {
        return $this->league;
    }

    public function setLeague(?SleeperLeague $league): SleeperTransaction
    {
        $this->league = $league;
        return $this;
    }

    public function buildFindByCriteriaFromDto(SleeperTransactionDto $sleeperTransactionDto): array
    {
        return [
            'transactionId' => $sleeperTransactionDto->getTransactionId(),
        ];
    }

    /**
     * @return Collection<int, SleeperRoster>
     */
    public function getRosters(): Collection
    {
        return $this->rosters;
    }

    public function addRoster(SleeperRoster $roster): static
    {
        if (!$this->rosters->contains($roster)) {
            $this->rosters->add($roster);
        }

        return $this;
    }

    public function removeRoster(SleeperRoster $roster): static
    {
        $this->rosters->removeElement($roster);

        return $this;
    }

    /**
     * @return Collection<int, SleeperPlayer>
     */
    public function getDroppedPlayers(): Collection
    {
        return $this->droppedPlayers;
    }

    public function addDroppedPlayer(SleeperPlayer $droppedPlayer): static
    {
        if (!$this->droppedPlayers->contains($droppedPlayer)) {
            $this->droppedPlayers->add($droppedPlayer);
        }

        return $this;
    }

    public function removeDroppedPlayer(SleeperPlayer $droppedPlayer): static
    {
        $this->droppedPlayers->removeElement($droppedPlayer);

        return $this;
    }

    public function getDroppedPlayerById(string $playerId): ?SleeperPlayer
    {
        foreach($this->droppedPlayers as $droppedPlayer) {
            if ($droppedPlayer->getPlayerId() === $playerId) {
                return $droppedPlayer;
            }
        }

        return null;
    }

    /**
     * @return Collection<int, SleeperPlayer>
     */
    public function getAddedPlayers(): Collection
    {
        return $this->addedPlayers;
    }

    public function addAddedPlayer(SleeperPlayer $addedPlayer): static
    {
        if (!$this->addedPlayers->contains($addedPlayer)) {
            $this->addedPlayers->add($addedPlayer);
        }

        return $this;
    }

    public function getAddedPlayerById(string $playerId): ?SleeperPlayer
    {
        foreach($this->addedPlayers as $addedPlayer) {
            if ($addedPlayer->getPlayerId() === $playerId) {
                return $addedPlayer;
            }
        }

        return null;
    }

    public function removeAddedPlayer(SleeperPlayer $addedPlayer): static
    {
        $this->addedPlayers->removeElement($addedPlayer);

        return $this;
    }

    /**
     * @return Collection<int, SleeperRoster>
     */
    public function getConsenterRosters(): Collection
    {
        return $this->consenterRosters;
    }

    public function addConsenterRoster(SleeperRoster $consenterRoster): static
    {
        if (!$this->consenterRosters->contains($consenterRoster)) {
            $this->consenterRosters->add($consenterRoster);
        }

        return $this;
    }

    public function removeConsenterRoster(SleeperRoster $consenterRoster): static
    {
        $this->consenterRosters->removeElement($consenterRoster);

        return $this;
    }
}
