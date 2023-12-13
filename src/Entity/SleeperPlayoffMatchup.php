<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiClient\Dto as Dto;

#[ORM\Entity]
#[ORM\UniqueConstraint(
    name: 'sasb_sleeper_playoff_matchup_unique',
    columns: ['league_id', 'branch', 'm']
)]
class SleeperPlayoffMatchup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $leagueId = null;

    #[ORM\Column]
    private ?string $branch = null;

    #[ORM\Column]
    private ?int $r = null;

    #[ORM\Column]
    private ?int $m = null;

    #[ORM\Column(nullable: true)]
    private ?int $t1 = null;

    #[ORM\Embedded(class: SleeperPlayoffMatchupSource::class, columnPrefix: 't1from_')]
    private ?SleeperPlayoffMatchupSource $t1From = null;

    #[ORM\Column(nullable: true)]
    private ?int $t2 = null;

    #[ORM\Embedded(class: SleeperPlayoffMatchupSource::class, columnPrefix: 't2from_')]
    private ?SleeperPlayoffMatchupSource $t2From = null;

    #[ORM\Column(nullable: true)]
    private ?int $w = null;

    #[ORM\Column(nullable: true)]
    private ?int $l = null;

    #[ORM\Column(nullable: true)]
    private ?int $p = null;

    #[ORM\ManyToOne(targetEntity: SleeperLeague::class, inversedBy: 'playoffMatchups')]
    #[ORM\JoinColumn(name: 'internal_league_id')]
    private ?SleeperLeague $league = null;

    #[ORM\ManyToOne(targetEntity: SleeperRoster::class, inversedBy: 'playoffMatchupsHome')]
    #[ORM\JoinColumn(name: 'internal_roster_id_t1')]
    private ?SleeperRoster $rosterTeam1 = null;

    #[ORM\ManyToOne(targetEntity: SleeperRoster::class, inversedBy: 'playoffMatchupsAway')]
    #[ORM\JoinColumn(name: 'internal_roster_id_t2')]
    private ?SleeperRoster $rosterTeam2 = null;

    #[ORM\OneToOne(targetEntity: SleeperMatchup::class)]
    #[ORM\JoinColumn(name: 'internal_matchup_id_t1')]
    private ?SleeperMatchup $matchupTeam1 = null;

    #[ORM\OneToOne(targetEntity: SleeperMatchup::class)]
    #[ORM\JoinColumn(name: 'internal_matchup_id_t2')]
    private ?SleeperMatchup $matchupTeam2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): SleeperPlayoffMatchup
    {
        $this->id = $id;
        return $this;
    }

    public function getLeagueId(): ?string
    {
        return $this->leagueId;
    }

    public function setLeagueId(?string $leagueId): SleeperPlayoffMatchup
    {
        $this->leagueId = $leagueId;
        return $this;
    }

    public function getBranch(): ?string
    {
        return $this->branch;
    }

    public function setBranch(?string $branch): SleeperPlayoffMatchup
    {
        $this->branch = $branch;
        return $this;
    }

    public function getR(): ?int
    {
        return $this->r;
    }

    public function setR(?int $r): SleeperPlayoffMatchup
    {
        $this->r = $r;
        return $this;
    }

    public function getM(): ?int
    {
        return $this->m;
    }

    public function setM(?int $m): SleeperPlayoffMatchup
    {
        $this->m = $m;
        return $this;
    }

    public function getT1(): ?int
    {
        return $this->t1;
    }

    public function setT1(?int $t1): SleeperPlayoffMatchup
    {
        $this->t1 = $t1;
        return $this;
    }

    public function getT1From(): ?SleeperPlayoffMatchupSource
    {
        return $this->t1From;
    }

    public function setT1From(?SleeperPlayoffMatchupSource $t1From): SleeperPlayoffMatchup
    {
        $this->t1From = $t1From;
        return $this;
    }

    public function getT2(): ?int
    {
        return $this->t2;
    }

    public function setT2(?int $t2): SleeperPlayoffMatchup
    {
        $this->t2 = $t2;
        return $this;
    }

    public function getT2From(): ?SleeperPlayoffMatchupSource
    {
        return $this->t2From;
    }

    public function setT2From(?SleeperPlayoffMatchupSource $t2From): SleeperPlayoffMatchup
    {
        $this->t2From = $t2From;
        return $this;
    }

    public function getW(): ?int
    {
        return $this->w;
    }

    public function setW(?int $w): SleeperPlayoffMatchup
    {
        $this->w = $w;
        return $this;
    }

    public function getL(): ?int
    {
        return $this->l;
    }

    public function setL(?int $l): SleeperPlayoffMatchup
    {
        $this->l = $l;
        return $this;
    }

    public function getP(): ?int
    {
        return $this->p;
    }

    public function setP(?int $p): SleeperPlayoffMatchup
    {
        $this->p = $p;
        return $this;
    }

    public function getLeague(): ?SleeperLeague
    {
        return $this->league;
    }

    public function setLeague(?SleeperLeague $league): SleeperPlayoffMatchup
    {
        $this->league = $league;
        return $this;
    }

    public function getRosterTeam1(): ?SleeperRoster
    {
        return $this->rosterTeam1;
    }

    public function setRosterTeam1(?SleeperRoster $roster): SleeperPlayoffMatchup
    {
        $this->rosterTeam1 = $roster;
        return $this;
    }

    public function getRosterTeam2(): ?SleeperRoster
    {
        return $this->rosterTeam2;
    }

    public function setRosterTeam2(?SleeperRoster $roster): SleeperPlayoffMatchup
    {
        $this->rosterTeam2 = $roster;
        return $this;
    }

    public function getMatchupTeam1(): ?SleeperMatchup
    {
        return $this->matchupTeam1;
    }

    public function setMatchupTeam1(?SleeperMatchup $matchupTeam1): SleeperPlayoffMatchup
    {
        $this->matchupTeam1 = $matchupTeam1;
        return $this;
    }

    public function getMatchupTeam2(): ?SleeperMatchup
    {
        return $this->matchupTeam2;
    }

    public function setMatchupTeam2(?SleeperMatchup $matchupTeam2): SleeperPlayoffMatchup
    {
        $this->matchupTeam2 = $matchupTeam2;
        return $this;
    }

    public function buildFindByCriteriaFromDto(string $leagueId, string $branch, Dto\SleeperPlayoffMatchup $sleeperPlayoffMatchupDto): array
    {
        return [
            'leagueId' => $leagueId,
            'branch' => $branch,
            'm' => $sleeperPlayoffMatchupDto->getM()
        ];
    }
}
