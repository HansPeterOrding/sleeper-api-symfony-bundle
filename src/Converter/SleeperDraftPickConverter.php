<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftPick as SleeperDraftPickDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick as SleeperDraftPickEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperDraftPickRepository;

class SleeperDraftPickConverter
{
    public function __construct(
        private readonly SleeperDraftPickRepository $sleeperDraftPickRepository,
        private readonly SleeperDraftPickMetadataConverter $sleeperDraftPickMetadataConverter
    )
    {
    }

    public function toEntity(SleeperDraftPickDto $sleeperDraftPickDto): SleeperDraftPickEntity
    {
        $sleeperDraftPickEntity = $this->sleeperDraftPickRepository->findByDtoOrCreateEntity($sleeperDraftPickDto);

        $sleeperDraftPickEntity->setRound($sleeperDraftPickDto->getRound());
        $sleeperDraftPickEntity->setRosterId($sleeperDraftPickDto->getRosterId());
        $sleeperDraftPickEntity->setPlayerId($sleeperDraftPickDto->getPlayerId());
        $sleeperDraftPickEntity->setPickedBy($sleeperDraftPickDto->getPickedBy());
        $sleeperDraftPickEntity->setPickNo($sleeperDraftPickDto->getPickNo());
        $sleeperDraftPickEntity->setIsKeeper($sleeperDraftPickDto->getIsKeeper());
        $sleeperDraftPickEntity->setDraftSlot($sleeperDraftPickDto->getDraftSlot());
        $sleeperDraftPickEntity->setDraftId($sleeperDraftPickDto->getDraftId());

        $sleeperDraftPickMetadataEntity = $this->sleeperDraftPickMetadataConverter->toEntity($sleeperDraftPickDto->getMetadata(), $sleeperDraftPickEntity->getMetadata());
        $sleeperDraftPickEntity->setMetadata($sleeperDraftPickMetadataEntity);

        return $sleeperDraftPickEntity;
    }
}
