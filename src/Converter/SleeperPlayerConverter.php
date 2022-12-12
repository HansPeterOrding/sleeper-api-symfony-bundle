<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayer as SleeperPlayerDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayer as SleeperPlayerEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerRepository;

class SleeperPlayerConverter
{
    public function __construct(
        private readonly SleeperPlayerRepository $sleeperPlayerRepository
    )
    {
    }

    public function toEntity(SleeperPlayerDto $sleeperPlayerDto): SleeperPlayerEntity
    {
        $sleeperPlayerEntity = $this->sleeperPlayerRepository->findByDtoOrCreateEntity($sleeperPlayerDto);

        $sleeperPlayerEntity->setPlayerId($sleeperPlayerDto->getPlayerId());
        $sleeperPlayerEntity->setFirstName($sleeperPlayerDto->getFirstName());
        $sleeperPlayerEntity->setLastName($sleeperPlayerDto->getLastName());
        $sleeperPlayerEntity->setAge($sleeperPlayerDto->getAge());
        $sleeperPlayerEntity->setTeam($sleeperPlayerDto->getTeam());
        $sleeperPlayerEntity->setNumber($sleeperPlayerDto->getNumber());
        $sleeperPlayerEntity->setStatus($sleeperPlayerDto->getStatus());
        $sleeperPlayerEntity->setActive($sleeperPlayerDto->isActive());
        $sleeperPlayerEntity->setPosition($sleeperPlayerDto->getPosition());
        $sleeperPlayerEntity->setFantasyPositions($sleeperPlayerDto->getFantasyPositions());
        $sleeperPlayerEntity->setDepthChartPosition($sleeperPlayerDto->getDepthChartPosition());
        $sleeperPlayerEntity->setDeptchChartOrder($sleeperPlayerDto->getDeptchChartOrder());
        $sleeperPlayerEntity->setWeight($sleeperPlayerDto->getWeight());
        $sleeperPlayerEntity->setHeight($sleeperPlayerDto->getHeight());
        $sleeperPlayerEntity->setHighSchool($sleeperPlayerDto->getHighSchool());
        $sleeperPlayerEntity->setCollege($sleeperPlayerDto->getCollege());
        $sleeperPlayerEntity->setBirthDate($sleeperPlayerDto->getBirthDate());
        $sleeperPlayerEntity->setBirthCity($sleeperPlayerDto->getBirthCity());
        $sleeperPlayerEntity->setBirthState($sleeperPlayerDto->getBirthState());
        $sleeperPlayerEntity->setBirthCountry($sleeperPlayerDto->getBirthCountry());
        $sleeperPlayerEntity->setYearsExp($sleeperPlayerDto->getYearsExp());
        $sleeperPlayerEntity->setEspnId($sleeperPlayerDto->getEspnId());
        $sleeperPlayerEntity->setFantasyDataId($sleeperPlayerDto->getFantasyDataId());
        $sleeperPlayerEntity->setGsisId($sleeperPlayerDto->getGsisId());
        $sleeperPlayerEntity->setPandascoreId($sleeperPlayerDto->getPandascoreId());
        $sleeperPlayerEntity->setRotowireId($sleeperPlayerDto->getRotowireId());
        $sleeperPlayerEntity->setRotoworldId($sleeperPlayerDto->getRotoworldId());
        $sleeperPlayerEntity->setSportradarId($sleeperPlayerDto->getSportradarId());
        $sleeperPlayerEntity->setStatsId($sleeperPlayerDto->getStatsId());
        $sleeperPlayerEntity->setSwishId($sleeperPlayerDto->getSwishId());
        $sleeperPlayerEntity->setYahooId($sleeperPlayerDto->getYahooId());
        $sleeperPlayerEntity->setInjuryStatus($sleeperPlayerDto->getInjuryStatus());

        return $sleeperPlayerEntity;
    }
}
