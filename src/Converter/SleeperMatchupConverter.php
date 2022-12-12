<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup as SleeperMatchupDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperMatchup as SleeperMatchupEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperMatchupRepository;

class SleeperMatchupConverter
{
    public function __construct(
        private readonly SleeperMatchupRepository $sleeperMatchupRepository
    )
    {
    }

    public function toEntity(string $leagueId, int $week, SleeperMatchupDto $sleeperMatchupDto): SleeperMatchupEntity
    {
        $sleeperMatchupEntity = $this->sleeperMatchupRepository->findByDtoOrCreateEntity($leagueId, $week, $sleeperMatchupDto);

        $sleeperMatchupEntity->setLeagueId($leagueId);
        $sleeperMatchupEntity->setWeek($week);
        $sleeperMatchupEntity->setStartersPoints($sleeperMatchupDto->getStartersPoints());
        $sleeperMatchupEntity->setPlayersPoints($sleeperMatchupDto->getPlayersPoints());
        $sleeperMatchupEntity->setStarters($sleeperMatchupDto->getStarters());
        $sleeperMatchupEntity->setRosterId($sleeperMatchupDto->getRosterId());
        $sleeperMatchupEntity->setPlayers($sleeperMatchupDto->getPlayers());
        $sleeperMatchupEntity->setMatchupId($sleeperMatchupDto->getMatchupId());
        $sleeperMatchupEntity->setPoints($sleeperMatchupDto->getPoints());
        $sleeperMatchupEntity->setCustomPoints($sleeperMatchupDto->getCustomPoints());

        return $sleeperMatchupEntity;
    }
}
