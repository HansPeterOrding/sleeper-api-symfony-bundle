<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperUserMetadata as SleeperUserMetadataDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUserMetadata as SleeperUserMetadataEntity;

class SleeperUserMetadataConverter
{
    public function toEntity(
        SleeperUserMetadataDto     $sleeperUserMetadataDto,
        ?SleeperUserMetadataEntity $sleeperUserMetadataEntity = null
    ): SleeperUserMetadataEntity {
        if (!$sleeperUserMetadataEntity) {
            $sleeperUserMetadataEntity = new SleeperUserMetadataEntity();
        }

        $sleeperUserMetadataEntity->setUserMessagePn($sleeperUserMetadataDto->getUserMessagePn());
        $sleeperUserMetadataEntity->setTransactionWaiver($sleeperUserMetadataDto->getTransactionWaiver());
        $sleeperUserMetadataEntity->setTransactionTrade($sleeperUserMetadataDto->getTransactionTrade());
        $sleeperUserMetadataEntity->setTransactionFreeAgent($sleeperUserMetadataDto->getTransactionFreeAgent());
        $sleeperUserMetadataEntity->setTransactionCommissioner($sleeperUserMetadataDto->getTransactionCommissioner());
        $sleeperUserMetadataEntity->setTradeBlockPn($sleeperUserMetadataDto->getTradeBlockPn());
        $sleeperUserMetadataEntity->setTeamName($sleeperUserMetadataDto->getTeamName());
        $sleeperUserMetadataEntity->setTeamNameUpdate($sleeperUserMetadataDto->getTeamNameUpdate());
        $sleeperUserMetadataEntity->setPlayerNicknameUpdate($sleeperUserMetadataDto->getPlayerNicknameUpdate());
        $sleeperUserMetadataEntity->setPlayerLikePn($sleeperUserMetadataDto->getPlayerLikePn());
        $sleeperUserMetadataEntity->setMentionPn($sleeperUserMetadataDto->getMentionPn());
        $sleeperUserMetadataEntity->setMascotMessage($sleeperUserMetadataDto->getMascotMessage());
        $sleeperUserMetadataEntity->setJoinVoicePn($sleeperUserMetadataDto->getJoinVoicePn());
        $sleeperUserMetadataEntity->setArchived($sleeperUserMetadataDto->getArchived());
        $sleeperUserMetadataEntity->setAllowPn($sleeperUserMetadataDto->getAllowPn());
        $sleeperUserMetadataEntity->setMascotMessageEmotionLeg1($sleeperUserMetadataDto->getMascotMessageEmotionLeg1());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg1($sleeperUserMetadataDto->getMascotItemTypeIdLeg1());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg2($sleeperUserMetadataDto->getMascotItemTypeIdLeg2());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg3($sleeperUserMetadataDto->getMascotItemTypeIdLeg3());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg4($sleeperUserMetadataDto->getMascotItemTypeIdLeg4());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg5($sleeperUserMetadataDto->getMascotItemTypeIdLeg5());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg6($sleeperUserMetadataDto->getMascotItemTypeIdLeg6());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg7($sleeperUserMetadataDto->getMascotItemTypeIdLeg7());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg8($sleeperUserMetadataDto->getMascotItemTypeIdLeg8());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg9($sleeperUserMetadataDto->getMascotItemTypeIdLeg9());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg10($sleeperUserMetadataDto->getMascotItemTypeIdLeg10());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg11($sleeperUserMetadataDto->getMascotItemTypeIdLeg11());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg12($sleeperUserMetadataDto->getMascotItemTypeIdLeg12());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg13($sleeperUserMetadataDto->getMascotItemTypeIdLeg13());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg14($sleeperUserMetadataDto->getMascotItemTypeIdLeg14());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg15($sleeperUserMetadataDto->getMascotItemTypeIdLeg15());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg16($sleeperUserMetadataDto->getMascotItemTypeIdLeg16());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg17($sleeperUserMetadataDto->getMascotItemTypeIdLeg17());
        $sleeperUserMetadataEntity->setMascotItemTypeIdLeg18($sleeperUserMetadataDto->getMascotItemTypeIdLeg18());

        return $sleeperUserMetadataEntity;
    }
}
