<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftPickMetadata as SleeperDraftPickMetadataDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\FantasyPositionEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\InjuryStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\PlayerStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SportEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPickMetadata as SleeperDraftPickMetadataEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperDraftPickMetadataRepository;

class SleeperDraftPickMetadataConverter
{
    public function toEntity(
        SleeperDraftPickMetadataDto     $sleeperDraftPickMetadataDto,
        ?SleeperDraftPickMetadataEntity $sleeperDraftPickMetadataEntity
    ): SleeperDraftPickMetadataEntity {
        if (!$sleeperDraftPickMetadataEntity) {
            $sleeperDraftPickMetadataEntity = new SleeperDraftPickMetadataEntity();
        }

        $sleeperDraftPickMetadataEntity->setYearsExp($sleeperDraftPickMetadataDto->getYearsExp());
        $sleeperDraftPickMetadataEntity->setTeam($sleeperDraftPickMetadataDto->getTeam());
        $sleeperDraftPickMetadataEntity->setStatus(PlayerStatusEnum::from($sleeperDraftPickMetadataDto->getStatus()));
        $sleeperDraftPickMetadataEntity->setSport(SportEnum::from($sleeperDraftPickMetadataDto->getSport()));
        $sleeperDraftPickMetadataEntity->setPosition(FantasyPositionEnum::from($sleeperDraftPickMetadataDto->getPosition()));
        $sleeperDraftPickMetadataEntity->setPlayerId($sleeperDraftPickMetadataDto->getPlayerId());
        $sleeperDraftPickMetadataEntity->setNumber($sleeperDraftPickMetadataDto->getNumber());
        $sleeperDraftPickMetadataEntity->setNewsUpdated($sleeperDraftPickMetadataDto->getNewsUpdated());
        $sleeperDraftPickMetadataEntity->setLastName($sleeperDraftPickMetadataDto->getLastName());
        $sleeperDraftPickMetadataEntity->setInjuryStatus(InjuryStatusEnum::from($sleeperDraftPickMetadataDto->getInjuryStatus()));
        $sleeperDraftPickMetadataEntity->setFirstName($sleeperDraftPickMetadataDto->getFirstName());
        $sleeperDraftPickMetadataEntity->setAmount($sleeperDraftPickMetadataDto->getAmount());

        return $sleeperDraftPickMetadataEntity;
    }
}
