<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperRosterSettings as SleeperRosterSettingsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRosterSettings as SleeperRosterSettingsEntity;

class SleeperRosterSettingsConverter
{
    public function toEntity(SleeperRosterSettingsDto $sleeperRosterSettingsDto, ?SleeperRosterSettingsEntity $sleeperRosterSettingsEntity = null): SleeperRosterSettingsEntity
    {
        if (!$sleeperRosterSettingsEntity) {
            $sleeperRosterSettingsEntity = new SleeperRosterSettingsEntity();
        }

        $sleeperRosterSettingsEntity->setWins($sleeperRosterSettingsDto->getWins());
        $sleeperRosterSettingsEntity->setWaiverPosition($sleeperRosterSettingsDto->getWaiverPosition());
        $sleeperRosterSettingsEntity->setWaiverBudgetUsed($sleeperRosterSettingsDto->getWaiverBudgetUsed());
        $sleeperRosterSettingsEntity->setTotalMoves($sleeperRosterSettingsDto->getTotalMoves());
        $sleeperRosterSettingsEntity->setTies($sleeperRosterSettingsDto->getTies());
        $sleeperRosterSettingsEntity->setLosses($sleeperRosterSettingsDto->getLosses());
        $sleeperRosterSettingsEntity->setFptsDecimal($sleeperRosterSettingsDto->getFptsDecimal());
        $sleeperRosterSettingsEntity->setFptsAgainstDecimal($sleeperRosterSettingsDto->getFptsAgainstDecimal());
        $sleeperRosterSettingsEntity->setFptsAgainst($sleeperRosterSettingsDto->getFptsAgainst());
        $sleeperRosterSettingsEntity->setFpts($sleeperRosterSettingsDto->getFpts());

        return $sleeperRosterSettingsEntity;
    }
}
