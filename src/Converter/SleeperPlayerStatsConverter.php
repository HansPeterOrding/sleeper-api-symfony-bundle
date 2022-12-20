<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats as SleeperPlayerStatsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayerStats as SleeperPlayerStatsEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerStatsRepository;

class SleeperPlayerStatsConverter
{
    public function __construct(
        private readonly SleeperPlayerStatsRepository $sleeperPlayerStatsRepository,
        private readonly SleeperStatsConverter $sleeperStatsConverter,
        private readonly SleeperPlayerConverter $sleeperPlayerConverter
    )
    {
    }

    public function toEntity(SleeperPlayerStatsDto $sleeperPlayerStatsDto): SleeperPlayerStatsEntity
    {
        $sleeperPlayerStatsEntity = $this->sleeperPlayerStatsRepository->findByDtoOrCreateEntity($sleeperPlayerStatsDto);

        $sleeperPlayerStatsEntity->setWeek($sleeperPlayerStatsDto->getWeek());
        $sleeperPlayerStatsEntity->setTeam($sleeperPlayerStatsDto->getTeam());
        $sleeperPlayerStatsEntity->setSport($sleeperPlayerStatsDto->getSport());
        $sleeperPlayerStatsEntity->setSeasonType($sleeperPlayerStatsDto->getSeasonType());
        $sleeperPlayerStatsEntity->setSeason($sleeperPlayerStatsDto->getSeason());
        $sleeperPlayerStatsEntity->setPlayerId($sleeperPlayerStatsDto->getPlayerId());
        $sleeperPlayerStatsEntity->setOpponent($sleeperPlayerStatsDto->getOpponent());
        $sleeperPlayerStatsEntity->setGameId($sleeperPlayerStatsDto->getGameId());
        $sleeperPlayerStatsEntity->setDate($sleeperPlayerStatsDto->getDate());
        $sleeperPlayerStatsEntity->setCompany($sleeperPlayerStatsDto->getCompany());
        $sleeperPlayerStatsEntity->setCategory($sleeperPlayerStatsDto->getCategory());

        $stats = $this->sleeperStatsConverter->toEntity($sleeperPlayerStatsDto->getStats(), $sleeperPlayerStatsEntity->getStats());
        $sleeperPlayerStatsEntity->setStats($stats);

        $playerDto = $sleeperPlayerStatsDto->getPlayer();
        $playerDto->setPlayerId($sleeperPlayerStatsDto->getPlayerId());
        $player = $this->sleeperPlayerConverter->toEntity($playerDto);
        $sleeperPlayerStatsEntity->setPlayer($player);

        return $sleeperPlayerStatsEntity;
    }
}
