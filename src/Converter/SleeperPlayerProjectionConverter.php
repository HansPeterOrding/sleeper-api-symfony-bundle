<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjection as SleeperPlayerProjectionDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayerProjection as SleeperPlayerProjectionEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerProjectionRepository;

class SleeperPlayerProjectionConverter
{
    public function __construct(
        private readonly SleeperPlayerProjectionRepository $sleeperPlayerProjectionRepository,
        private readonly SleeperStatsConverter $sleeperStatsConverter,
        private readonly SleeperPlayerConverter $sleeperPlayerConverter
    )
    {
    }

    public function toEntity(SleeperPlayerProjectionDto $sleeperPlayerProjectionDto): SleeperPlayerProjectionEntity
    {
        $sleeperPlayerProjectionEntity = $this->sleeperPlayerProjectionRepository->findByDtoOrCreateEntity($sleeperPlayerProjectionDto);

        $sleeperPlayerProjectionEntity->setWeek($sleeperPlayerProjectionDto->getWeek());
        $sleeperPlayerProjectionEntity->setTeam($sleeperPlayerProjectionDto->getTeam());
        $sleeperPlayerProjectionEntity->setSport($sleeperPlayerProjectionDto->getSport());
        $sleeperPlayerProjectionEntity->setSeasonType($sleeperPlayerProjectionDto->getSeasonType());
        $sleeperPlayerProjectionEntity->setSeason($sleeperPlayerProjectionDto->getSeason());
        $sleeperPlayerProjectionEntity->setPlayerId($sleeperPlayerProjectionDto->getPlayerId());
        $sleeperPlayerProjectionEntity->setOpponent($sleeperPlayerProjectionDto->getOpponent());
        $sleeperPlayerProjectionEntity->setGameId($sleeperPlayerProjectionDto->getGameId());
        $sleeperPlayerProjectionEntity->setDate($sleeperPlayerProjectionDto->getDate());
        $sleeperPlayerProjectionEntity->setCompany($sleeperPlayerProjectionDto->getCompany());
        $sleeperPlayerProjectionEntity->setCategory($sleeperPlayerProjectionDto->getCategory());

        $stats = $this->sleeperStatsConverter->toEntity($sleeperPlayerProjectionDto->getStats(), $sleeperPlayerProjectionEntity->getStats());
        $sleeperPlayerProjectionEntity->setStats($stats);

        $playerDto = $sleeperPlayerProjectionDto->getPlayer();
        $playerDto->setPlayerId($sleeperPlayerProjectionDto->getPlayerId());
        $player = $this->sleeperPlayerConverter->toEntity($playerDto);
        $sleeperPlayerProjectionEntity->setPlayer($player);

        return $sleeperPlayerProjectionEntity;
    }
}
