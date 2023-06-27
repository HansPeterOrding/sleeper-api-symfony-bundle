<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftMetadata as SleeperDraftMetadataDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\ScoringTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftMetadata as SleeperDraftMetadataEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperDraftMetadataRepository;

class SleeperDraftMetadataConverter
{
    public function toEntity(SleeperDraftMetadataDto $sleeperDraftMetadataDto, ?SleeperDraftMetadataEntity $sleeperDraftMetadataEntity): SleeperDraftMetadataEntity
    {
        if(!$sleeperDraftMetadataEntity) {
            $sleeperDraftMetadataEntity = new SleeperDraftMetadataEntity();
        }

        $sleeperDraftMetadataEntity->setScoringType(ScoringTypeEnum::from($sleeperDraftMetadataDto->getScoringType()));
        $sleeperDraftMetadataEntity->setName($sleeperDraftMetadataDto->getName());
        $sleeperDraftMetadataEntity->setElapsedPickTimer($sleeperDraftMetadataDto->getElapsedPickTimer());
        $sleeperDraftMetadataEntity->setDescription($sleeperDraftMetadataDto->getDescription());

        return $sleeperDraftMetadataEntity;
    }
}
