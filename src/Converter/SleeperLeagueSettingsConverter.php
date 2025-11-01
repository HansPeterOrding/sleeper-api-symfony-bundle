<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperLeagueSettings as SleeperLeagueSettingsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeagueSettings as SleeperLeagueSettingsEntity;

class SleeperLeagueSettingsConverter implements ConverterInterface {
    public function toEntity(
        SleeperLeagueSettingsDto     $sleeperLeagueSettingsDto,
        ?SleeperLeagueSettingsEntity $sleeperLeagueSettingsEntity = null
    ): SleeperLeagueSettingsEntity
    {
        if (!$sleeperLeagueSettingsEntity) {
            $sleeperLeagueSettingsEntity = new SleeperLeagueSettingsEntity();
        }

        $sleeperLeagueSettingsEntity->setWasAutoArchived($sleeperLeagueSettingsDto->getWasAutoArchived());
        $sleeperLeagueSettingsEntity->setWaiverType($sleeperLeagueSettingsDto->getWaiverType());
        $sleeperLeagueSettingsEntity->setWaiverDayOfWeek($sleeperLeagueSettingsDto->getWaiverDayOfWeek());
        $sleeperLeagueSettingsEntity->setWaiverClearDays($sleeperLeagueSettingsDto->getWaiverClearDays());
        $sleeperLeagueSettingsEntity->setWaiverBudget($sleeperLeagueSettingsDto->getWaiverBudget());
        $sleeperLeagueSettingsEntity->setType($sleeperLeagueSettingsDto->getType());
        $sleeperLeagueSettingsEntity->setTradeReviewDays($sleeperLeagueSettingsDto->getTradeReviewDays());
        $sleeperLeagueSettingsEntity->setTradeDeadline($sleeperLeagueSettingsDto->getTradeDeadline());
        $sleeperLeagueSettingsEntity->setTaxiYears($sleeperLeagueSettingsDto->getTaxiYears());
        $sleeperLeagueSettingsEntity->setTaxiSlots($sleeperLeagueSettingsDto->getTaxiSlots());
        $sleeperLeagueSettingsEntity->setTaxiDeadline($sleeperLeagueSettingsDto->getTaxiDeadline());
        $sleeperLeagueSettingsEntity->setTaxiAllowVets($sleeperLeagueSettingsDto->getTaxiAllowVets());
        $sleeperLeagueSettingsEntity->setStartWeek($sleeperLeagueSettingsDto->getStartWeek());
        $sleeperLeagueSettingsEntity->setReserveSlots($sleeperLeagueSettingsDto->getReserveSlots());
        $sleeperLeagueSettingsEntity->setReserveAllowSus($sleeperLeagueSettingsDto->getReserveAllowSus());
        $sleeperLeagueSettingsEntity->setReserveAllowOut($sleeperLeagueSettingsDto->getReserveAllowOut());
        $sleeperLeagueSettingsEntity->setReserveAllowDoubtful($sleeperLeagueSettingsDto->getReserveAllowDoubtful());
        $sleeperLeagueSettingsEntity->setPlayoffWeekStart($sleeperLeagueSettingsDto->getPlayoffWeekStart());
        $sleeperLeagueSettingsEntity->setPlayoffType($sleeperLeagueSettingsDto->getPlayoffType());
        $sleeperLeagueSettingsEntity->setPlayoffRoundType($sleeperLeagueSettingsDto->getPlayoffRoundType());
        $sleeperLeagueSettingsEntity->setPlayoffTeams($sleeperLeagueSettingsDto->getPlayoffTeams());
        $sleeperLeagueSettingsEntity->setPickTrading($sleeperLeagueSettingsDto->getPickTrading());
        $sleeperLeagueSettingsEntity->setOffseasonAdds($sleeperLeagueSettingsDto->getOffseasonAdds());
        $sleeperLeagueSettingsEntity->setNumTeams($sleeperLeagueSettingsDto->getNumTeams());
        $sleeperLeagueSettingsEntity->setMaxKeepers($sleeperLeagueSettingsDto->getMaxKeepers());
        $sleeperLeagueSettingsEntity->setLeg($sleeperLeagueSettingsDto->getLeg());
        $sleeperLeagueSettingsEntity->setLeagueAverageMatch($sleeperLeagueSettingsDto->getLeagueAverageMatch());
        $sleeperLeagueSettingsEntity->setLastScoredLeg($sleeperLeagueSettingsDto->getLastScoredLeg());
        $sleeperLeagueSettingsEntity->setLastReport($sleeperLeagueSettingsDto->getLastReport());
        $sleeperLeagueSettingsEntity->setDraftRounds($sleeperLeagueSettingsDto->getDraftRounds());
        $sleeperLeagueSettingsEntity->setDailyWaiversHour($sleeperLeagueSettingsDto->getDailyWaiversHour());
        $sleeperLeagueSettingsEntity->setDailyWaivers($sleeperLeagueSettingsDto->getDailyWaivers());
        $sleeperLeagueSettingsEntity->setBenchLock($sleeperLeagueSettingsDto->getBenchLock());
        $sleeperLeagueSettingsEntity->setBestBall($sleeperLeagueSettingsDto->getBestBall());
        $sleeperLeagueSettingsEntity->setDisableAdds($sleeperLeagueSettingsDto->getDisableAdds());
        $sleeperLeagueSettingsEntity->setDivisions($sleeperLeagueSettingsDto->getDivisions());
        $sleeperLeagueSettingsEntity->setCapacityOverride($sleeperLeagueSettingsDto->getCapacityOverride());
        $sleeperLeagueSettingsEntity->setWaiverBidMin($sleeperLeagueSettingsDto->getWaiverBidMin());
        $sleeperLeagueSettingsEntity->setReserveAllowNa($sleeperLeagueSettingsDto->getReserveAllowNa());
        $sleeperLeagueSettingsEntity->setPlayoffSeedType($sleeperLeagueSettingsDto->getPlayoffSeedType());
        $sleeperLeagueSettingsEntity->setVetoVotesNeeded($sleeperLeagueSettingsDto->getVetoVotesNeeded());
        $sleeperLeagueSettingsEntity->setSubStartTimeEligibility($sleeperLeagueSettingsDto->getSubStartTimeEligibility());
        $sleeperLeagueSettingsEntity->setDailyWaiversDays($sleeperLeagueSettingsDto->getDailyWaiversDays());
        $sleeperLeagueSettingsEntity->setSubLockIfStarterActive($sleeperLeagueSettingsDto->getSubLockIfStarterActive());
        $sleeperLeagueSettingsEntity->setCommissionerDirectInvite($sleeperLeagueSettingsDto->getCommissionerDirectInvite());
        $sleeperLeagueSettingsEntity->setVetoAutoPoll($sleeperLeagueSettingsDto->getVetoAutoPoll());
        $sleeperLeagueSettingsEntity->setReserveAllowDnr($sleeperLeagueSettingsDto->getReserveAllowDnr());
        $sleeperLeagueSettingsEntity->setVetoShowVotes($sleeperLeagueSettingsDto->getVetoShowVotes());
        $sleeperLeagueSettingsEntity->setFaabSuggestions($sleeperLeagueSettingsDto->getFaabSuggestions());
        $sleeperLeagueSettingsEntity->setDisableTrades($sleeperLeagueSettingsDto->getDisableTrades());
        $sleeperLeagueSettingsEntity->setMaxSubs($sleeperLeagueSettingsDto->getMaxSubs());
        $sleeperLeagueSettingsEntity->setDailyWaiversBase($sleeperLeagueSettingsDto->getDailyWaiversBase());
        $sleeperLeagueSettingsEntity->setReserveAllowCov($sleeperLeagueSettingsDto->getReserveAllowCov());
        $sleeperLeagueSettingsEntity->setDailyWaiversLastRan($sleeperLeagueSettingsDto->getDailyWaiversLastRan());

        return $sleeperLeagueSettingsEntity;
    }
}
