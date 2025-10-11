<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class SleeperLeagueSettings {
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $wasAutoArchived = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $waiverType = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $waiverDayOfWeek = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $waiverClearDays = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $waiverBudget = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $type = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $tradeReviewDays = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $tradeDeadline = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $taxiYears = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $taxiSlots = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $taxiDeadline = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $taxiAllowVets = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $startWeek = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reserveSlots = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reserveAllowSus = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reserveAllowOut = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reserveAllowDoubtful = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $playoffWeekStart = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $playoffType = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $playoffRoundType = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $playoffTeams = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $pickTrading = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $offseasonAdds = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $numTeams = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $maxKeepers = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $leg = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $leagueAverageMatch = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lastScoredLeg = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lastReport = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $draftRounds = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $dailyWaiversHour = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $dailyWaivers = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $benchLock = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $bestBall = null;

    public function getWasAutoArchived(): ?int
    {
        return $this->wasAutoArchived;
    }

    public function setWasAutoArchived(?int $wasAutoArchived): SleeperLeagueSettings
    {
        $this->wasAutoArchived = $wasAutoArchived;
        return $this;
    }

    public function getWaiverType(): ?int
    {
        return $this->waiverType;
    }

    public function setWaiverType(?int $waiverType): SleeperLeagueSettings
    {
        $this->waiverType = $waiverType;
        return $this;
    }

    public function getWaiverDayOfWeek(): ?int
    {
        return $this->waiverDayOfWeek;
    }

    public function setWaiverDayOfWeek(?int $waiverDayOfWeek): SleeperLeagueSettings
    {
        $this->waiverDayOfWeek = $waiverDayOfWeek;
        return $this;
    }

    public function getWaiverClearDays(): ?int
    {
        return $this->waiverClearDays;
    }

    public function setWaiverClearDays(?int $waiverClearDays): SleeperLeagueSettings
    {
        $this->waiverClearDays = $waiverClearDays;
        return $this;
    }

    public function getWaiverBudget(): ?int
    {
        return $this->waiverBudget;
    }

    public function setWaiverBudget(?int $waiverBudget): SleeperLeagueSettings
    {
        $this->waiverBudget = $waiverBudget;
        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): SleeperLeagueSettings
    {
        $this->type = $type;
        return $this;
    }

    public function getTradeReviewDays(): ?int
    {
        return $this->tradeReviewDays;
    }

    public function setTradeReviewDays(?int $tradeReviewDays): SleeperLeagueSettings
    {
        $this->tradeReviewDays = $tradeReviewDays;
        return $this;
    }

    public function getTradeDeadline(): ?int
    {
        return $this->tradeDeadline;
    }

    public function setTradeDeadline(?int $tradeDeadline): SleeperLeagueSettings
    {
        $this->tradeDeadline = $tradeDeadline;
        return $this;
    }

    public function getTaxiYears(): ?int
    {
        return $this->taxiYears;
    }

    public function setTaxiYears(?int $taxiYears): SleeperLeagueSettings
    {
        $this->taxiYears = $taxiYears;
        return $this;
    }

    public function getTaxiSlots(): ?int
    {
        return $this->taxiSlots;
    }

    public function setTaxiSlots(?int $taxiSlots): SleeperLeagueSettings
    {
        $this->taxiSlots = $taxiSlots;
        return $this;
    }

    public function getTaxiDeadline(): ?int
    {
        return $this->taxiDeadline;
    }

    public function setTaxiDeadline(?int $taxiDeadline): SleeperLeagueSettings
    {
        $this->taxiDeadline = $taxiDeadline;
        return $this;
    }

    public function getTaxiAllowVets(): ?int
    {
        return $this->taxiAllowVets;
    }

    public function setTaxiAllowVets(?int $taxiAllowVets): SleeperLeagueSettings
    {
        $this->taxiAllowVets = $taxiAllowVets;
        return $this;
    }

    public function getStartWeek(): ?int
    {
        return $this->startWeek;
    }

    public function setStartWeek(?int $startWeek): SleeperLeagueSettings
    {
        $this->startWeek = $startWeek;
        return $this;
    }

    public function getReserveSlots(): ?int
    {
        return $this->reserveSlots;
    }

    public function setReserveSlots(?int $reserveSlots): SleeperLeagueSettings
    {
        $this->reserveSlots = $reserveSlots;
        return $this;
    }

    public function getReserveAllowSus(): ?int
    {
        return $this->reserveAllowSus;
    }

    public function setReserveAllowSus(?int $reserveAllowSus): SleeperLeagueSettings
    {
        $this->reserveAllowSus = $reserveAllowSus;
        return $this;
    }

    public function getReserveAllowOut(): ?int
    {
        return $this->reserveAllowOut;
    }

    public function setReserveAllowOut(?int $reserveAllowOut): SleeperLeagueSettings
    {
        $this->reserveAllowOut = $reserveAllowOut;
        return $this;
    }

    public function getReserveAllowDoubtful(): ?int
    {
        return $this->reserveAllowDoubtful;
    }

    public function setReserveAllowDoubtful(?int $reserveAllowDoubtful): SleeperLeagueSettings
    {
        $this->reserveAllowDoubtful = $reserveAllowDoubtful;
        return $this;
    }

    public function getPlayoffWeekStart(): ?int
    {
        return $this->playoffWeekStart;
    }

    public function setPlayoffWeekStart(?int $playoffWeekStart): SleeperLeagueSettings
    {
        $this->playoffWeekStart = $playoffWeekStart;
        return $this;
    }

    public function getPlayoffType(): ?int
    {
        return $this->playoffType;
    }

    public function setPlayoffType(?int $playoffType): SleeperLeagueSettings
    {
        $this->playoffType = $playoffType;
        return $this;
    }

    public function getPlayoffRoundType(): ?int
    {
        return $this->playoffRoundType;
    }

    public function setPlayoffRoundType(?int $playoffRoundType): SleeperLeagueSettings
    {
        $this->playoffRoundType = $playoffRoundType;
        return $this;
    }

    public function getPlayoffTeams(): ?int
    {
        return $this->playoffTeams;
    }

    public function setPlayoffTeams(?int $playoffTeams): SleeperLeagueSettings
    {
        $this->playoffTeams = $playoffTeams;
        return $this;
    }

    public function getPickTrading(): ?int
    {
        return $this->pickTrading;
    }

    public function setPickTrading(?int $pickTrading): SleeperLeagueSettings
    {
        $this->pickTrading = $pickTrading;
        return $this;
    }

    public function getOffseasonAdds(): ?int
    {
        return $this->offseasonAdds;
    }

    public function setOffseasonAdds(?int $offseasonAdds): SleeperLeagueSettings
    {
        $this->offseasonAdds = $offseasonAdds;
        return $this;
    }

    public function getNumTeams(): ?int
    {
        return $this->numTeams;
    }

    public function setNumTeams(?int $numTeams): SleeperLeagueSettings
    {
        $this->numTeams = $numTeams;
        return $this;
    }

    public function getMaxKeepers(): ?int
    {
        return $this->maxKeepers;
    }

    public function setMaxKeepers(?int $maxKeepers): SleeperLeagueSettings
    {
        $this->maxKeepers = $maxKeepers;
        return $this;
    }

    public function getLeg(): ?int
    {
        return $this->leg;
    }

    public function setLeg(?int $leg): SleeperLeagueSettings
    {
        $this->leg = $leg;
        return $this;
    }

    public function getLeagueAverageMatch(): ?int
    {
        return $this->leagueAverageMatch;
    }

    public function setLeagueAverageMatch(?int $leagueAverageMatch): SleeperLeagueSettings
    {
        $this->leagueAverageMatch = $leagueAverageMatch;
        return $this;
    }

    public function getLastScoredLeg(): ?int
    {
        return $this->lastScoredLeg;
    }

    public function setLastScoredLeg(?int $lastScoredLeg): SleeperLeagueSettings
    {
        $this->lastScoredLeg = $lastScoredLeg;
        return $this;
    }

    public function getLastReport(): ?int
    {
        return $this->lastReport;
    }

    public function setLastReport(?int $lastReport): SleeperLeagueSettings
    {
        $this->lastReport = $lastReport;
        return $this;
    }

    public function getDraftRounds(): ?int
    {
        return $this->draftRounds;
    }

    public function setDraftRounds(?int $draftRounds): SleeperLeagueSettings
    {
        $this->draftRounds = $draftRounds;
        return $this;
    }

    public function getDailyWaiversHour(): ?int
    {
        return $this->dailyWaiversHour;
    }

    public function setDailyWaiversHour(?int $dailyWaiversHour): SleeperLeagueSettings
    {
        $this->dailyWaiversHour = $dailyWaiversHour;
        return $this;
    }

    public function getDailyWaivers(): ?int
    {
        return $this->dailyWaivers;
    }

    public function setDailyWaivers(?int $dailyWaivers): SleeperLeagueSettings
    {
        $this->dailyWaivers = $dailyWaivers;
        return $this;
    }

    public function getBenchLock(): ?int
    {
        return $this->benchLock;
    }

    public function setBenchLock(?int $benchLock): SleeperLeagueSettings
    {
        $this->benchLock = $benchLock;
        return $this;
    }

    public function getBestBall(): ?int
    {
        return $this->bestBall;
    }

    public function setBestBall(?int $bestBall): SleeperLeagueSettings
    {
        $this->bestBall = $bestBall;
        return $this;
    }
}
