<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperDraft as SleeperDraftDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\DraftStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\DraftTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SeasonTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SportEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft as SleeperDraftEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperDraftRepository;

class SleeperDraftConverter
{
    public function __construct(
        private readonly SleeperDraftRepository        $sleeperDraftRepository,
        private readonly SleeperDraftSettingsConverter $sleeperDraftSettingsConverter,
        private readonly SleeperDraftMetadataConverter $sleeperDraftMetadataConverter
    ) {
    }

    public function toEntity(SleeperDraftDto $sleeperDraftDto): SleeperDraftEntity
    {
        $sleeperDraftEntity = $this->sleeperDraftRepository->findByDtoOrCreateEntity($sleeperDraftDto);

        $sleeperDraftEntity->setType(DraftTypeEnum::from($sleeperDraftDto->getType()));
        $sleeperDraftEntity->setStatus(DraftStatusEnum::from($sleeperDraftDto->getStatus()));
        $sleeperDraftEntity->setStartTime($sleeperDraftDto->getStartTime());
        $sleeperDraftEntity->setSport(SportEnum::from($sleeperDraftDto->getSport()));
        $sleeperDraftEntity->setSlotToRosterId($sleeperDraftDto->getSlotToRosterId());
        $sleeperDraftEntity->setSeasonType(SeasonTypeEnum::from($sleeperDraftDto->getSeasonType()));
        $sleeperDraftEntity->setSeason($sleeperDraftDto->getSeason());
        $sleeperDraftEntity->setLeagueId($sleeperDraftDto->getLeagueId());
        $sleeperDraftEntity->setLastPicked($sleeperDraftDto->getLastPicked());
        $sleeperDraftEntity->setLastMessageTime($sleeperDraftDto->getLastMessageTime());
        $sleeperDraftEntity->setLastMessageId($sleeperDraftDto->getLastMessageId());
        $sleeperDraftEntity->setDraftOrder($sleeperDraftDto->getDraftOrder());
        $sleeperDraftEntity->setDraftId($sleeperDraftDto->getDraftId());
        $sleeperDraftEntity->setCreators($sleeperDraftDto->getCreators());
        $sleeperDraftEntity->setCreated($sleeperDraftDto->getCreated());

        $sleeperDraftSettingsEntity = $this->sleeperDraftSettingsConverter->toEntity(
            $sleeperDraftDto->getSettings(),
            $sleeperDraftEntity->getSettings()
        );
        $sleeperDraftEntity->setSettings($sleeperDraftSettingsEntity);

        $sleeperDraftMetadataEntity = $this->sleeperDraftMetadataConverter->toEntity(
            $sleeperDraftDto->getMetadata(),
            $sleeperDraftEntity->getMetadata()
        );
        $sleeperDraftEntity->setMetadata($sleeperDraftMetadataEntity);

        return $sleeperDraftEntity;
    }
}
