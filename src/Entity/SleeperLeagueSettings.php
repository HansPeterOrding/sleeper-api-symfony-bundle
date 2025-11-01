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

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $disableAdds = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $divisions = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $capacityOverride = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $waiverBidMin = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reserveAllowNa = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $playoffSeedType = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $vetoVotesNeeded = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subStartTimeEligibility = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $dailyWaiversDays = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $subLockIfStarterActive = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $commissionerDirectInvite = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $vetoAutoPoll = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reserveAllowDnr = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $vetoShowVotes = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $faabSuggestions = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $disableTrades = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $maxSubs = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $dailyWaiversBase = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $reserveAllowCov = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $dailyWaiversLastRan = null;

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

    public function getDisableAdds(): ?int
    {
        return $this->disableAdds;
    }

    public function setDisableAdds(?int $disableAdds): void
    {
        $this->disableAdds = $disableAdds;
    }

    public function getDivisions(): ?int
    {
        return $this->divisions;
    }

    public function setDivisions(?int $divisions): void
    {
        $this->divisions = $divisions;
    }

    public function getCapacityOverride(): ?int
    {
        return $this->capacityOverride;
    }

    public function setCapacityOverride(?int $capacityOverride): void
    {
        $this->capacityOverride = $capacityOverride;
    }

    public function getWaiverBidMin(): ?int
    {
        return $this->waiverBidMin;
    }

    public function setWaiverBidMin(?int $waiverBidMin): void
    {
        $this->waiverBidMin = $waiverBidMin;
    }

    public function getReserveAllowNa(): ?int
    {
        return $this->reserveAllowNa;
    }

    public function setReserveAllowNa(?int $reserveAllowNa): void
    {
        $this->reserveAllowNa = $reserveAllowNa;
    }

    public function getPlayoffSeedType(): ?int
    {
        return $this->playoffSeedType;
    }

    public function setPlayoffSeedType(?int $playoffSeedType): void
    {
        $this->playoffSeedType = $playoffSeedType;
    }

    public function getVetoVotesNeeded(): ?int
    {
        return $this->vetoVotesNeeded;
    }

    public function setVetoVotesNeeded(?int $vetoVotesNeeded): void
    {
        $this->vetoVotesNeeded = $vetoVotesNeeded;
    }

    public function getSubStartTimeEligibility(): ?int
    {
        return $this->subStartTimeEligibility;
    }

    public function setSubStartTimeEligibility(?int $subStartTimeEligibility): void
    {
        $this->subStartTimeEligibility = $subStartTimeEligibility;
    }

    public function getDailyWaiversDays(): ?int
    {
        return $this->dailyWaiversDays;
    }

    public function setDailyWaiversDays(?int $dailyWaiversDays): void
    {
        $this->dailyWaiversDays = $dailyWaiversDays;
    }

    public function getSubLockIfStarterActive(): ?int
    {
        return $this->subLockIfStarterActive;
    }

    public function setSubLockIfStarterActive(?int $subLockIfStarterActive): void
    {
        $this->subLockIfStarterActive = $subLockIfStarterActive;
    }

    public function getCommissionerDirectInvite(): ?int
    {
        return $this->commissionerDirectInvite;
    }

    public function setCommissionerDirectInvite(?int $commissionerDirectInvite): void
    {
        $this->commissionerDirectInvite = $commissionerDirectInvite;
    }

    public function getVetoAutoPoll(): ?int
    {
        return $this->vetoAutoPoll;
    }

    public function setVetoAutoPoll(?int $vetoAutoPoll): void
    {
        $this->vetoAutoPoll = $vetoAutoPoll;
    }

    public function getReserveAllowDnr(): ?int
    {
        return $this->reserveAllowDnr;
    }

    public function setReserveAllowDnr(?int $reserveAllowDnr): void
    {
        $this->reserveAllowDnr = $reserveAllowDnr;
    }

    public function getVetoShowVotes(): ?int
    {
        return $this->vetoShowVotes;
    }

    public function setVetoShowVotes(?int $vetoShowVotes): void
    {
        $this->vetoShowVotes = $vetoShowVotes;
    }

    public function getFaabSuggestions(): ?int
    {
        return $this->faabSuggestions;
    }

    public function setFaabSuggestions(?int $faabSuggestions): void
    {
        $this->faabSuggestions = $faabSuggestions;
    }

    public function getDisableTrades(): ?int
    {
        return $this->disableTrades;
    }

    public function setDisableTrades(?int $disableTrades): void
    {
        $this->disableTrades = $disableTrades;
    }

    public function getMaxSubs(): ?int
    {
        return $this->maxSubs;
    }

    public function setMaxSubs(?int $maxSubs): void
    {
        $this->maxSubs = $maxSubs;
    }

    public function getDailyWaiversBase(): ?int
    {
        return $this->dailyWaiversBase;
    }

    public function setDailyWaiversBase(?int $dailyWaiversBase): void
    {
        $this->dailyWaiversBase = $dailyWaiversBase;
    }

    public function getReserveAllowCov(): ?int
    {
        return $this->reserveAllowCov;
    }

    public function setReserveAllowCov(?int $reserveAllowCov): void
    {
        $this->reserveAllowCov = $reserveAllowCov;
    }

    public function getDailyWaiversLastRan(): ?int
    {
        return $this->dailyWaiversLastRan;
    }

    public function setDailyWaiversLastRan(?int $dailyWaiversLastRan): void
    {
        $this->dailyWaiversLastRan = $dailyWaiversLastRan;
    }
}
