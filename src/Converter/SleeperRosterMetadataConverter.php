<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperRosterMetadata as SleeperRosterMetadataDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRosterMetadata as SleeperRosterMetadataEntity;

class SleeperRosterMetadataConverter implements ConverterInterface {
    public function toEntity(
        SleeperRosterMetadataDto     $sleeperRosterMetadataDto,
        ?SleeperRosterMetadataEntity $sleeperRosterMetadataEntity = null
    ): SleeperRosterMetadataEntity
    {
        if (!$sleeperRosterMetadataEntity) {
            $sleeperRosterMetadataEntity = new SleeperRosterMetadataEntity();
        }

        $sleeperRosterMetadataEntity->setAllowPnInactiveStarters($sleeperRosterMetadataDto->getAllowPnInactiveStarters());
        $sleeperRosterMetadataEntity->setAllowPnPlayerInjuryStatus($sleeperRosterMetadataDto->getAllowPnPlayerInjuryStatus());
        $sleeperRosterMetadataEntity->setAllowPnScoring($sleeperRosterMetadataDto->getAllowPnScoring());
        $sleeperRosterMetadataEntity->setRecord($sleeperRosterMetadataDto->getRecord());
        $sleeperRosterMetadataEntity->setRestrictPnScoringStartersOnly($sleeperRosterMetadataDto->getRestrictPnScoringStartersOnly());
        $sleeperRosterMetadataEntity->setStreak($sleeperRosterMetadataDto->getStreak());
        $sleeperRosterMetadataEntity->setPlayerNicknames($sleeperRosterMetadataDto->getPlayerNicknames());

        return $sleeperRosterMetadataEntity;
    }
}
