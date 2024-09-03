<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperTransactionSettings as SleeperTransactionSettingsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransactionSettings as SleeperTransactionSettingsEntity;

class SleeperTransactionSettingsConverter
{
    public function toEntity(
        SleeperTransactionSettingsDto     $sleeperTransactionSettingsDto,
        ?SleeperTransactionSettingsEntity $sleeperTransactionSettingsEntity
    ): SleeperTransactionSettingsEntity {
        if (!$sleeperTransactionSettingsEntity) {
            $sleeperTransactionSettingsEntity = new SleeperTransactionSettingsEntity();
        }

        $sleeperTransactionSettingsEntity->setWaiverBid($sleeperTransactionSettingsDto->getWaiverBid());
        $sleeperTransactionSettingsEntity->setSeq($sleeperTransactionSettingsDto->getSeq());
        $sleeperTransactionSettingsEntity->setPriority($sleeperTransactionSettingsDto->getPriority());

        return $sleeperTransactionSettingsEntity;
    }
}
