<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftSettings as SleeperDraftSettingsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\DraftStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\DraftTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SeasonTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SportEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftSettings as SleeperDraftSettingsEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperDraftSettingsRepository;

class SleeperDraftSettingsConverter
{
    public function __construct(
        private readonly SleeperDraftSettingsRepository $sleeperDraftSettingsRepository
    )
    {
    }

    public function toEntity(SleeperDraftSettingsDto $sleeperDraftSettingsDto, ?SleeperDraftSettingsEntity $sleeperDraftSettingsEntity): SleeperDraftSettingsEntity
    {
        if(!$sleeperDraftSettingsEntity) {
            $sleeperDraftSettingsEntity = new SleeperDraftSettingsEntity();
        }

        $sleeperDraftSettingsEntity->setTeams($sleeperDraftSettingsDto->getTeams());
        $sleeperDraftSettingsEntity->setSlotsWr($sleeperDraftSettingsDto->getSlotsWr());
        $sleeperDraftSettingsEntity->setSlotsTe($sleeperDraftSettingsDto->getSlotsTe());
        $sleeperDraftSettingsEntity->setSlotsSuperFlex($sleeperDraftSettingsDto->getSlotsSuperFlex());
        $sleeperDraftSettingsEntity->setSlotsRecFlex($sleeperDraftSettingsDto->getSlotsRecFlex());
        $sleeperDraftSettingsEntity->setSlotsRb($sleeperDraftSettingsDto->getSlotsRb());
        $sleeperDraftSettingsEntity->setSlotsQb($sleeperDraftSettingsDto->getSlotsQb());
        $sleeperDraftSettingsEntity->setSlotsLb($sleeperDraftSettingsDto->getSlotsLb());
        $sleeperDraftSettingsEntity->setSlotsK($sleeperDraftSettingsDto->getSlotsK());
        $sleeperDraftSettingsEntity->setSlotsIdpFlex($sleeperDraftSettingsDto->getSlotsIdpFlex());
        $sleeperDraftSettingsEntity->setSlotsFlex($sleeperDraftSettingsDto->getSlotsFlex());
        $sleeperDraftSettingsEntity->setSlotsDl($sleeperDraftSettingsDto->getSlotsDl());
        $sleeperDraftSettingsEntity->setSlotsDb($sleeperDraftSettingsDto->getSlotsDb());
        $sleeperDraftSettingsEntity->setSlotsDef($sleeperDraftSettingsDto->getSlotsDef());
        $sleeperDraftSettingsEntity->setSlotsBn($sleeperDraftSettingsDto->getSlotsBn());
        $sleeperDraftSettingsEntity->setRounds($sleeperDraftSettingsDto->getRounds());
        $sleeperDraftSettingsEntity->setReversalRound($sleeperDraftSettingsDto->getReversalRound());
        $sleeperDraftSettingsEntity->setPlayerType($sleeperDraftSettingsDto->getPlayerType());
        $sleeperDraftSettingsEntity->setPickTimer($sleeperDraftSettingsDto->getPickTimer());
        $sleeperDraftSettingsEntity->setNominationTimer($sleeperDraftSettingsDto->getNominationTimer());
        $sleeperDraftSettingsEntity->setEnforcePositionLimits($sleeperDraftSettingsDto->getEnforcePositionLimits());
        $sleeperDraftSettingsEntity->setCpuAutopick($sleeperDraftSettingsDto->getCpuAutopick());
        $sleeperDraftSettingsEntity->setAlphaSort($sleeperDraftSettingsDto->getAlphaSort());

        return $sleeperDraftSettingsEntity;
    }
}
