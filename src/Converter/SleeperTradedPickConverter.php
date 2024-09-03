<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperTradedPick as SleeperTradedPickDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTradedPick as SleeperTradedPickEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperTradedPickRepository;

class SleeperTradedPickConverter implements ConverterInterface
{
    public function __construct(
        private readonly SleeperTradedPickRepository $sleeperTradedPickRepository
    ) {
    }

    public function toEntity(string $leagueId, string $draftId, SleeperTradedPickDto $sleeperTradedPickDto): SleeperTradedPickEntity
    {
        $sleeperTradedPickEntity = $this->sleeperTradedPickRepository->findByDtoOrCreateEntity($leagueId, $draftId, $sleeperTradedPickDto);

        $sleeperTradedPickEntity->setSeason($sleeperTradedPickDto->getSeason());
        $sleeperTradedPickEntity->setRound($sleeperTradedPickDto->getRound());
        $sleeperTradedPickEntity->setRosterId($sleeperTradedPickDto->getRosterId());
        $sleeperTradedPickEntity->setPreviousOwnerId($sleeperTradedPickDto->getPreviousOwnerId());
        $sleeperTradedPickEntity->setOwnerId($sleeperTradedPickDto->getOwnerId());
        $sleeperTradedPickEntity->setDraftId($draftId);
        $sleeperTradedPickEntity->setLeagueId($leagueId);

        return $sleeperTradedPickEntity;
    }
}
