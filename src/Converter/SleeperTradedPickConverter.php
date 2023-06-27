<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperTradedPick as SleeperTradedPickDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTradedPick as SleeperTradedPickEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperTradedPickRepository;

class SleeperTradedPickConverter
{
    public function __construct(
        private readonly SleeperTradedPickRepository $sleeperTradedPickRepository
    )
    {
    }

    public function toEntity(SleeperTradedPickDto $sleeperTradedPickDto): SleeperTradedPickEntity
    {
        $sleeperTradedPickEntity = $this->sleeperTradedPickRepository->findByDtoOrCreateEntity($sleeperTradedPickDto);

        $sleeperTradedPickEntity->setSeason($sleeperTradedPickDto->getSeason());
        $sleeperTradedPickEntity->setRound($sleeperTradedPickDto->getRound());
        $sleeperTradedPickEntity->setRosterId($sleeperTradedPickDto->getRosterId());
        $sleeperTradedPickEntity->setPreviousOwnerId($sleeperTradedPickDto->getPreviousOwnerId());
        $sleeperTradedPickEntity->setOwnerId($sleeperTradedPickDto->getOwnerId());
        $sleeperTradedPickEntity->setDraftId($sleeperTradedPickDto->getDraftId());
        $sleeperTradedPickEntity->setLeagueId($sleeperTradedPickDto->getLeagueId());

        return $sleeperTradedPickEntity;
    }
}
