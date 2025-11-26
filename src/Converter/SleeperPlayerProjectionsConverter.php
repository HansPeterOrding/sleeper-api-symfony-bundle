<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjections as SleeperPlayerProjectionsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayerProjections as SleeperPlayerProjectionsEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerProjectionsRepository;

class SleeperPlayerProjectionsConverter implements ConverterInterface {
    public function __construct(
        private readonly SleeperPlayerProjectionsRepository $sleeperPlayerProjectionsRepository,
        private readonly SleeperStatsConverter             $sleeperStatsConverter,
        private readonly SleeperPlayerConverter            $sleeperPlayerConverter
    )
    {
    }

    public function toEntity(SleeperPlayerProjectionsDto $sleeperPlayerProjectionsDto): SleeperPlayerProjectionsEntity
    {
        $sleeperPlayerProjectionsEntity = $this->sleeperPlayerProjectionsRepository->findByDtoOrCreateEntity($sleeperPlayerProjectionsDto);

        $sleeperPlayerProjectionsEntity->setWeek($sleeperPlayerProjectionsDto->getWeek());
        $sleeperPlayerProjectionsEntity->setTeam($sleeperPlayerProjectionsDto->getTeam());
        $sleeperPlayerProjectionsEntity->setSport($sleeperPlayerProjectionsDto->getSport());
        $sleeperPlayerProjectionsEntity->setSeasonType($sleeperPlayerProjectionsDto->getSeasonType());
        $sleeperPlayerProjectionsEntity->setSeason($sleeperPlayerProjectionsDto->getSeason());
        $sleeperPlayerProjectionsEntity->setPlayerId($sleeperPlayerProjectionsDto->getPlayerId());
        $sleeperPlayerProjectionsEntity->setOpponent($sleeperPlayerProjectionsDto->getOpponent());
        $sleeperPlayerProjectionsEntity->setGameId($sleeperPlayerProjectionsDto->getGameId());
        $sleeperPlayerProjectionsEntity->setDate($sleeperPlayerProjectionsDto->getDate());
        $sleeperPlayerProjectionsEntity->setCompany($sleeperPlayerProjectionsDto->getCompany());
        $sleeperPlayerProjectionsEntity->setCategory($sleeperPlayerProjectionsDto->getCategory());

        $stats = $this->sleeperStatsConverter->toEntity($sleeperPlayerProjectionsDto->getStats(), $sleeperPlayerProjectionsEntity->getStats());
        $sleeperPlayerProjectionsEntity->setStats($stats);

        $playerDto = $sleeperPlayerProjectionsDto->getPlayer();
        $playerDto->setPlayerId($sleeperPlayerProjectionsDto->getPlayerId());
        $player = $this->sleeperPlayerConverter->toEntity($playerDto);
        $sleeperPlayerProjectionsEntity->setPlayer($player);

        return $sleeperPlayerProjectionsEntity;
    }
}
