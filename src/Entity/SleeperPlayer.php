<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayer as SleeperPlayerDto;

#[ORM\Entity]
#[ORM\Table(name: 'sasb_sleeper_player')]
#[ORM\UniqueConstraint(name: 'uniq_sasb_sleeper_player_player_id', columns: ['player_id'])]
#[ORM\Index(name: 'idx_sasb_sleeper_player_id_player_id', columns: ['id', 'player_id'])]
class SleeperPlayer {
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    #[ORM\Column]
    private string $playerId;

    #[ORM\Column(nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\Column(nullable: true)]
    private ?string $team = null;

    #[ORM\Column(nullable: true)]
    private ?int $number = null;

    #[ORM\Column(nullable: true)]
    private ?string $status = null;

    #[ORM\Column]
    private bool $active = true;

    #[ORM\Column(nullable: true)]
    private ?string $position = null;

    #[ORM\Column(nullable: true)]
    private ?array $fantasyPositions = null;

    #[ORM\Column(nullable: true)]
    private ?string $depthChartPosition = null;

    #[ORM\Column(nullable: true)]
    private ?int $deptchChartOrder = null;

    #[ORM\Column(nullable: true)]
    private ?string $weight = null;

    #[ORM\Column(nullable: true)]
    private ?string $height = null;

    #[ORM\Column(nullable: true)]
    private ?string $highSchool = null;

    #[ORM\Column(nullable: true)]
    private ?string $college = null;

    #[ORM\Column(nullable: true)]
    private ?DateTime $birthDate = null;

    #[ORM\Column(nullable: true)]
    private ?string $birthCity = null;

    #[ORM\Column(nullable: true)]
    private ?string $birthState = null;

    #[ORM\Column(nullable: true)]
    private ?string $birthCountry = null;

    #[ORM\Column(nullable: true)]
    private ?int $yearsExp = null;

    #[ORM\Column(nullable: true)]
    private ?int $espnId = null;

    #[ORM\Column(nullable: true)]
    private ?int $searchRank = null;

    #[ORM\Column(nullable: true)]
    private ?int $fantasyDataId = null;

    #[ORM\Column(nullable: true)]
    private ?string $gsisId = null;

    #[ORM\Column(nullable: true)]
    private ?int $pandascoreId = null;

    #[ORM\Column(nullable: true)]
    private ?int $rotowireId = null;

    #[ORM\Column(nullable: true)]
    private ?int $rotoworldId = null;

    #[ORM\Column(nullable: true)]
    private ?string $sportradarId = null;

    #[ORM\Column(nullable: true)]
    private ?int $statsId = null;

    #[ORM\Column(nullable: true)]
    private ?int $swishId = null;

    #[ORM\Column(nullable: true)]
    private ?int $yahooId = null;

    #[ORM\Column(nullable: true)]
    private ?string $injuryStatus = null;

    #[ORM\Column(nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(nullable: true)]
    private ?string $hashtag = null;

    #[ORM\Column(nullable: true)]
    private ?string $searchFirstName = null;

    #[ORM\Column(nullable: true)]
    private ?string $searchLastName = null;

    #[ORM\Column(nullable: true)]
    private ?string $searchFullName = null;

    #[ORM\Column(nullable: true)]
    private ?string $sport = null;

    #[ORM\Column(nullable: true)]
    private ?string $playerShard = null;

    // ms-Epoch (z. B. 1653351613345) — bigint, Muster wie $id
    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $newsUpdated = null;

    #[ORM\Column(nullable: true)]
    private ?string $injuryBodyPart = null;

    // Freitext -> TEXT (Konvention aus dem ESPN-Bundle)
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $injuryNotes = null;

    // im Feed bisher nur null beobachtet; bewusst string, Format unverifiziert
    #[ORM\Column(nullable: true)]
    private ?string $injuryStartDate = null;

    // Freitext -> TEXT
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $practiceDescription = null;

    #[ORM\Column(nullable: true)]
    private ?string $practiceParticipation = null;

    #[ORM\Column(nullable: true)]
    private ?string $kalshiId = null;

    #[ORM\Column(nullable: true)]
    private ?string $oddsjamId = null;

    // im Feed bisher nur null beobachtet
    #[ORM\Column(nullable: true)]
    private ?string $optaId = null;

    // im Feed bisher nur null beobachtet
    #[ORM\Column(nullable: true)]
    private ?string $teamAbbr = null;

    // im Feed bisher nur null beobachtet; vermutlich Timestamp-String
    #[ORM\Column(nullable: true)]
    private ?string $teamChangedAt = null;

    // rohes Sleeper-Metadata-Objekt (channel_id, rookie_year, injury_override_* u. a.) -> json
    #[ORM\Column(nullable: true)]
    private ?array $metadata = null;

    // -> json
    #[ORM\Column(nullable: true)]
    private ?array $competitions = null;

    /**
     * @var Collection<int, SleeperDraftPick>
     */
    #[ORM\OneToMany(mappedBy: 'player', targetEntity: SleeperDraftPick::class)]
    private Collection $draftPicks;

    public function __construct()
    {
        $this->draftPicks = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): SleeperPlayer
    {
        $this->id = $id;
        return $this;
    }

    public function getPlayerId(): string
    {
        return $this->playerId;
    }

    public function setPlayerId(string $playerId): SleeperPlayer
    {
        $this->playerId = $playerId;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): SleeperPlayer
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): SleeperPlayer
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): SleeperPlayer
    {
        $this->age = $age;
        return $this;
    }

    public function getTeam(): ?string
    {
        return $this->team;
    }

    public function setTeam(?string $team): SleeperPlayer
    {
        $this->team = $team;
        return $this;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(?int $number): SleeperPlayer
    {
        $this->number = $number;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): SleeperPlayer
    {
        $this->status = $status;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): SleeperPlayer
    {
        $this->active = $active;
        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): SleeperPlayer
    {
        $this->position = $position;
        return $this;
    }

    public function getFantasyPositions(): ?array
    {
        return $this->fantasyPositions;
    }

    public function setFantasyPositions(?array $fantasyPositions): SleeperPlayer
    {
        $this->fantasyPositions = $fantasyPositions;
        return $this;
    }

    public function getDepthChartPosition(): ?string
    {
        return $this->depthChartPosition;
    }

    public function setDepthChartPosition(?string $depthChartPosition): SleeperPlayer
    {
        $this->depthChartPosition = $depthChartPosition;
        return $this;
    }

    public function getDeptchChartOrder(): ?int
    {
        return $this->deptchChartOrder;
    }

    public function setDeptchChartOrder(?int $deptchChartOrder): SleeperPlayer
    {
        $this->deptchChartOrder = $deptchChartOrder;
        return $this;
    }

    public function getWeight(): ?string
    {
        return $this->weight;
    }

    public function setWeight(?string $weight): SleeperPlayer
    {
        $this->weight = $weight;
        return $this;
    }

    public function getHeight(): ?string
    {
        return $this->height;
    }

    public function setHeight(?string $height): SleeperPlayer
    {
        $this->height = $height;
        return $this;
    }

    public function getHighSchool(): ?string
    {
        return $this->highSchool;
    }

    public function setHighSchool(?string $highSchool): SleeperPlayer
    {
        $this->highSchool = $highSchool;
        return $this;
    }

    public function getCollege(): ?string
    {
        return $this->college;
    }

    public function setCollege(?string $college): SleeperPlayer
    {
        $this->college = $college;
        return $this;
    }

    public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }

    public function setBirthDate(?DateTime $birthDate): SleeperPlayer
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function getBirthCity(): ?string
    {
        return $this->birthCity;
    }

    public function setBirthCity(?string $birthCity): SleeperPlayer
    {
        $this->birthCity = $birthCity;
        return $this;
    }

    public function getBirthState(): ?string
    {
        return $this->birthState;
    }

    public function setBirthState(?string $birthState): SleeperPlayer
    {
        $this->birthState = $birthState;
        return $this;
    }

    public function getBirthCountry(): ?string
    {
        return $this->birthCountry;
    }

    public function setBirthCountry(?string $birthCountry): SleeperPlayer
    {
        $this->birthCountry = $birthCountry;
        return $this;
    }

    public function getYearsExp(): ?int
    {
        return $this->yearsExp;
    }

    public function setYearsExp(?int $yearsExp): SleeperPlayer
    {
        $this->yearsExp = $yearsExp;
        return $this;
    }

    public function getEspnId(): ?int
    {
        return $this->espnId;
    }

    public function setEspnId(?int $espnId): SleeperPlayer
    {
        $this->espnId = $espnId;
        return $this;
    }

    public function getSearchRank(): ?int
    {
        return $this->searchRank;
    }

    public function setSearchRank(?int $searchRank): SleeperPlayer
    {
        $this->searchRank = $searchRank;
        return $this;
    }

    public function getFantasyDataId(): ?int
    {
        return $this->fantasyDataId;
    }

    public function setFantasyDataId(?int $fantasyDataId): SleeperPlayer
    {
        $this->fantasyDataId = $fantasyDataId;
        return $this;
    }

    public function getGsisId(): ?string
    {
        return $this->gsisId;
    }

    public function setGsisId(?string $gsisId): SleeperPlayer
    {
        $this->gsisId = $gsisId;
        return $this;
    }

    public function getPandascoreId(): ?int
    {
        return $this->pandascoreId;
    }

    public function setPandascoreId(?int $pandascoreId): SleeperPlayer
    {
        $this->pandascoreId = $pandascoreId;
        return $this;
    }

    public function getRotowireId(): ?int
    {
        return $this->rotowireId;
    }

    public function setRotowireId(?int $rotowireId): SleeperPlayer
    {
        $this->rotowireId = $rotowireId;
        return $this;
    }

    public function getRotoworldId(): ?int
    {
        return $this->rotoworldId;
    }

    public function setRotoworldId(?int $rotoworldId): SleeperPlayer
    {
        $this->rotoworldId = $rotoworldId;
        return $this;
    }

    public function getSportradarId(): ?string
    {
        return $this->sportradarId;
    }

    public function setSportradarId(?string $sportradarId): SleeperPlayer
    {
        $this->sportradarId = $sportradarId;
        return $this;
    }

    public function getStatsId(): ?int
    {
        return $this->statsId;
    }

    public function setStatsId(?int $statsId): SleeperPlayer
    {
        $this->statsId = $statsId;
        return $this;
    }

    public function getSwishId(): ?int
    {
        return $this->swishId;
    }

    public function setSwishId(?int $swishId): SleeperPlayer
    {
        $this->swishId = $swishId;
        return $this;
    }

    public function getYahooId(): ?int
    {
        return $this->yahooId;
    }

    public function setYahooId(?int $yahooId): SleeperPlayer
    {
        $this->yahooId = $yahooId;
        return $this;
    }

    public function getInjuryStatus(): ?string
    {
        return $this->injuryStatus;
    }

    public function setInjuryStatus(?string $injuryStatus): SleeperPlayer
    {
        $this->injuryStatus = $injuryStatus;
        return $this;
    }

    public function getDraftPicks(): Collection
    {
        return $this->draftPicks;
    }

    public function setDraftPicks(Collection $draftPicks): SleeperPlayer
    {
        $this->draftPicks = $draftPicks;
        return $this;
    }

    public function buildFindByCriteriaFromDto(SleeperPlayerDto $sleeperPlayerDto): array
    {
        return [
            'playerId' => $sleeperPlayerDto->getPlayerId()
        ];
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): SleeperPlayer
    {
        $this->fullName = $fullName;
        return $this;
    }

    public function getHashtag(): ?string
    {
        return $this->hashtag;
    }

    public function setHashtag(?string $hashtag): SleeperPlayer
    {
        $this->hashtag = $hashtag;
        return $this;
    }

    public function getSearchFirstName(): ?string
    {
        return $this->searchFirstName;
    }

    public function setSearchFirstName(?string $searchFirstName): SleeperPlayer
    {
        $this->searchFirstName = $searchFirstName;
        return $this;
    }

    public function getSearchLastName(): ?string
    {
        return $this->searchLastName;
    }

    public function setSearchLastName(?string $searchLastName): SleeperPlayer
    {
        $this->searchLastName = $searchLastName;
        return $this;
    }

    public function getSearchFullName(): ?string
    {
        return $this->searchFullName;
    }

    public function setSearchFullName(?string $searchFullName): SleeperPlayer
    {
        $this->searchFullName = $searchFullName;
        return $this;
    }

    public function getSport(): ?string
    {
        return $this->sport;
    }

    public function setSport(?string $sport): SleeperPlayer
    {
        $this->sport = $sport;
        return $this;
    }

    public function getPlayerShard(): ?string
    {
        return $this->playerShard;
    }

    public function setPlayerShard(?string $playerShard): SleeperPlayer
    {
        $this->playerShard = $playerShard;
        return $this;
    }

    public function getNewsUpdated(): ?int
    {
        return $this->newsUpdated;
    }

    public function setNewsUpdated(?int $newsUpdated): SleeperPlayer
    {
        $this->newsUpdated = $newsUpdated;
        return $this;
    }

    public function getInjuryBodyPart(): ?string
    {
        return $this->injuryBodyPart;
    }

    public function setInjuryBodyPart(?string $injuryBodyPart): SleeperPlayer
    {
        $this->injuryBodyPart = $injuryBodyPart;
        return $this;
    }

    public function getInjuryNotes(): ?string
    {
        return $this->injuryNotes;
    }

    public function setInjuryNotes(?string $injuryNotes): SleeperPlayer
    {
        $this->injuryNotes = $injuryNotes;
        return $this;
    }

    public function getInjuryStartDate(): ?string
    {
        return $this->injuryStartDate;
    }

    public function setInjuryStartDate(?string $injuryStartDate): SleeperPlayer
    {
        $this->injuryStartDate = $injuryStartDate;
        return $this;
    }

    public function getPracticeDescription(): ?string
    {
        return $this->practiceDescription;
    }

    public function setPracticeDescription(?string $practiceDescription): SleeperPlayer
    {
        $this->practiceDescription = $practiceDescription;
        return $this;
    }

    public function getPracticeParticipation(): ?string
    {
        return $this->practiceParticipation;
    }

    public function setPracticeParticipation(?string $practiceParticipation): SleeperPlayer
    {
        $this->practiceParticipation = $practiceParticipation;
        return $this;
    }

    public function getKalshiId(): ?string
    {
        return $this->kalshiId;
    }

    public function setKalshiId(?string $kalshiId): SleeperPlayer
    {
        $this->kalshiId = $kalshiId;
        return $this;
    }

    public function getOddsjamId(): ?string
    {
        return $this->oddsjamId;
    }

    public function setOddsjamId(?string $oddsjamId): SleeperPlayer
    {
        $this->oddsjamId = $oddsjamId;
        return $this;
    }

    public function getOptaId(): ?string
    {
        return $this->optaId;
    }

    public function setOptaId(?string $optaId): SleeperPlayer
    {
        $this->optaId = $optaId;
        return $this;
    }

    public function getTeamAbbr(): ?string
    {
        return $this->teamAbbr;
    }

    public function setTeamAbbr(?string $teamAbbr): SleeperPlayer
    {
        $this->teamAbbr = $teamAbbr;
        return $this;
    }

    public function getTeamChangedAt(): ?string
    {
        return $this->teamChangedAt;
    }

    public function setTeamChangedAt(?string $teamChangedAt): SleeperPlayer
    {
        $this->teamChangedAt = $teamChangedAt;
        return $this;
    }

    public function getMetadata(): ?array
    {
        return $this->metadata;
    }

    public function setMetadata(?array $metadata): SleeperPlayer
    {
        $this->metadata = $metadata;
        return $this;
    }

    public function getCompetitions(): ?array
    {
        return $this->competitions;
    }

    public function setCompetitions(?array $competitions): SleeperPlayer
    {
        $this->competitions = $competitions;
        return $this;
    }
}
