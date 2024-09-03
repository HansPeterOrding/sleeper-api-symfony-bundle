<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayoffMatchup as SleeperPlayoffMatchupDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup as SleeperPlayoffMatchupEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayoffMatchupRepository;

class SleeperPlayoffMatchupConverter implements ConverterInterface
{
    public function __construct(
        private readonly SleeperPlayoffMatchupRepository      $sleeperPlayoffMatchupRepository,
        private readonly SleeperPlayoffMatchupSourceConverter $sleeperPlayoffMatchupSourceConverter
    ) {
    }

    public function toEntity(
        string                   $leagueId,
        string                   $branch,
        SleeperPlayoffMatchupDto $sleeperPlayoffMatchupDto
    ): SleeperPlayoffMatchupEntity {
        $sleeperPlayoffMatchupEntity = $this->sleeperPlayoffMatchupRepository->findByDtoOrCreateEntity($leagueId, $branch, $sleeperPlayoffMatchupDto);

        $sleeperPlayoffMatchupEntity->setLeagueId($leagueId);
        $sleeperPlayoffMatchupEntity->setBranch($branch);
        $sleeperPlayoffMatchupEntity->setR($sleeperPlayoffMatchupDto->getR());
        $sleeperPlayoffMatchupEntity->setM($sleeperPlayoffMatchupDto->getM());
        $sleeperPlayoffMatchupEntity->setT1($sleeperPlayoffMatchupDto->getT1());
        $sleeperPlayoffMatchupEntity->setT2($sleeperPlayoffMatchupDto->getT2());
        $sleeperPlayoffMatchupEntity->setW($sleeperPlayoffMatchupDto->getW());
        $sleeperPlayoffMatchupEntity->setL($sleeperPlayoffMatchupDto->getL());
        $sleeperPlayoffMatchupEntity->setP($sleeperPlayoffMatchupDto->getP());

        $sleeperPlayoffMatchupSourceT1Entity = $this->sleeperPlayoffMatchupSourceConverter->toEntity(
            $sleeperPlayoffMatchupDto->getT1From(),
            $sleeperPlayoffMatchupEntity->getT1From()
        );
        $sleeperPlayoffMatchupEntity->setT1From($sleeperPlayoffMatchupSourceT1Entity);

        $sleeperPlayoffMatchupSourceT2Entity = $this->sleeperPlayoffMatchupSourceConverter->toEntity(
            $sleeperPlayoffMatchupDto->getT2From(),
            $sleeperPlayoffMatchupEntity->getT2From()
        );
        $sleeperPlayoffMatchupEntity->setT2From($sleeperPlayoffMatchupSourceT2Entity);

        return $sleeperPlayoffMatchupEntity;
    }
}
