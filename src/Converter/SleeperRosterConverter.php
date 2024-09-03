<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperRoster as SleeperRosterDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster as SleeperRosterEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperRosterRepository;

class SleeperRosterConverter implements ConverterInterface
{
    public function __construct(
        private readonly SleeperRosterRepository        $sleeperRosterRepository,
        private readonly SleeperRosterSettingsConverter $sleeperRosterSettingsConverter
    ) {
    }

    public function toEntity(SleeperRosterDto $sleeperRosterDto): SleeperRosterEntity
    {
        $sleeperRosterEntity = $this->sleeperRosterRepository->findByDtoOrCreateEntity($sleeperRosterDto);

        $sleeperRosterEntity->setRosterId($sleeperRosterDto->getRosterId());
        $sleeperRosterEntity->setOwnerId($sleeperRosterDto->getOwnerId());
        $sleeperRosterEntity->setLeagueId($sleeperRosterDto->getLeagueId());
        $sleeperRosterEntity->setStarters($sleeperRosterDto->getStarters());
        $sleeperRosterEntity->setReserve($sleeperRosterDto->getReserve());
        $sleeperRosterEntity->setPlayers($sleeperRosterDto->getPlayers());

        $sleeperRosterSettingsEntity = $this->sleeperRosterSettingsConverter->toEntity(
            $sleeperRosterDto->getSettings(),
            $sleeperRosterEntity->getSettings()
        );
        $sleeperRosterEntity->setSettings($sleeperRosterSettingsEntity);

        $sleeperRosterEntity->setCoOwners($sleeperRosterDto->getCoOwners());

        return $sleeperRosterEntity;
    }
}
