<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperNflState as SleeperNflStateDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SeasonTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperNflState as SleeperNflStateEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperNflStateRepository;

class SleeperNflStateConverter implements ConverterInterface {
    public function __construct(
        private readonly SleeperNflStateRepository $sleeperNflStateRepository,
    )
    {
    }

    public function toEntity(SleeperNflStateDto $sleeperNflStateDto): SleeperNflStateEntity
    {
        $sleeperNflStateEntity = $this->sleeperNflStateRepository->findByDtoOrCreateEntity($sleeperNflStateDto);

        $sleeperNflStateEntity->setWeek($sleeperNflStateDto->getWeek());
        $sleeperNflStateEntity->setSeasonType(SeasonTypeEnum::from($sleeperNflStateDto->getSeasonType()));
        $sleeperNflStateEntity->setSeasonStartDate($sleeperNflStateDto->getSeasonStartDate());
        $sleeperNflStateEntity->setSeason($sleeperNflStateDto->getSeason());
        $sleeperNflStateEntity->setPreviousSeason($sleeperNflStateDto->getPreviousSeason());
        $sleeperNflStateEntity->setLeg($sleeperNflStateDto->getLeg());
        $sleeperNflStateEntity->setLeagueSeason($sleeperNflStateDto->getLeagueSeason());
        $sleeperNflStateEntity->setLeagueCreateSeason($sleeperNflStateDto->getLeagueCreateSeason());
        $sleeperNflStateEntity->setDisplayWeek($sleeperNflStateDto->getWeek());

        return $sleeperNflStateEntity;
    }
}
