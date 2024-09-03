<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperLeague as SleeperLeagueDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\LeagueStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SeasonTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague as SleeperLeagueEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperLeagueRepository;

class SleeperLeagueConverter implements ConverterInterface {
    public function __construct(
        private readonly SleeperLeagueRepository               $sleeperLeagueRepository,
        private readonly SleeperLeagueSettingsConverter        $sleeperLeagueSettingsConverter,
        private readonly SleeperLeagueScoringSettingsConverter $sleeperLeagueScoringSettingsConverter
    )
    {
    }

    public function toEntity(SleeperLeagueDto $sleeperLeagueDto): SleeperLeagueEntity
    {
        $sleeperLeagueEntity = $this->sleeperLeagueRepository->findByDtoOrCreateEntity($sleeperLeagueDto);

        $sleeperLeagueEntity->setTotalRosters($sleeperLeagueDto->getTotalRosters());
        $sleeperLeagueEntity->setStatus(LeagueStatusEnum::from($sleeperLeagueDto->getStatus()));
        $sleeperLeagueEntity->setSport($sleeperLeagueDto->getSport());
        $sleeperLeagueEntity->setSeasonType(SeasonTypeEnum::from($sleeperLeagueDto->getSeasonType()));
        $sleeperLeagueEntity->setSeason($sleeperLeagueDto->getSeason());
        $sleeperLeagueEntity->setRosterPositions($sleeperLeagueDto->getRosterPositions());
        $sleeperLeagueEntity->setPreviousLeagueId($sleeperLeagueDto->getPreviousLeagueId());
        $sleeperLeagueEntity->setName($sleeperLeagueDto->getName());
        $sleeperLeagueEntity->setLeagueId($sleeperLeagueDto->getLeagueId());
        $sleeperLeagueEntity->setDraftId($sleeperLeagueDto->getDraftId());
        $sleeperLeagueEntity->setAvatar($sleeperLeagueDto->getAvatar());

        $sleeperLeagueSettingsEntity = $this->sleeperLeagueSettingsConverter->toEntity(
            $sleeperLeagueDto->getSettings(),
            $sleeperLeagueEntity->getSettings()
        );
        $sleeperLeagueEntity->setSettings($sleeperLeagueSettingsEntity);

        $sleeperLeagueScoringSettingsEntity = $this->sleeperLeagueScoringSettingsConverter->toEntity(
            $sleeperLeagueDto->getScoringSettings(),
            $sleeperLeagueEntity->getScoringSettings()
        );
        $sleeperLeagueEntity->setScoringSettings($sleeperLeagueScoringSettingsEntity);

        return $sleeperLeagueEntity;
    }
}
