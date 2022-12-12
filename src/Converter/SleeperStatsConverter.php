<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperStats as SleeperStatsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperStats as SleeperStatsEntity;

class SleeperStatsConverter
{
    public function toEntity(SleeperStatsDto $sleeperStatsDto, ?SleeperStatsEntity $sleeperStatsEntity = null): SleeperStatsEntity
    {
        if (!$sleeperStatsEntity) {
            $sleeperStatsEntity = new SleeperStatsEntity();
        }

        $sleeperStatsEntity->setGp($sleeperStatsDto->getGp());
        $sleeperStatsEntity->setGmsActive($sleeperStatsDto->getGmsActive());

        $sleeperStatsEntity->setRushYd($sleeperStatsDto->getRushYd());
        $sleeperStatsEntity->setRushTd($sleeperStatsDto->getRushTd());
        $sleeperStatsEntity->setRushFd($sleeperStatsDto->getRushFd());
        $sleeperStatsEntity->setRushAtt($sleeperStatsDto->getRushAtt());
        $sleeperStatsEntity->setRush40p($sleeperStatsDto->getRush40p());

        $sleeperStatsEntity->setRush2pt($sleeperStatsDto->getRush2pt());
        $sleeperStatsEntity->setBonusRushRecYd100($sleeperStatsDto->getBonusRushRecYd100());
        $sleeperStatsEntity->setRecYd($sleeperStatsDto->getRecYd());
        $sleeperStatsEntity->setRecTdLng($sleeperStatsDto->getRecTdLng());
        $sleeperStatsEntity->setRec3039($sleeperStatsDto->getRec3039());
        $sleeperStatsEntity->setRecRzTgt($sleeperStatsDto->getRecRzTgt());
        $sleeperStatsEntity->setRecYpt($sleeperStatsDto->getRecYpt());
        $sleeperStatsEntity->setRecAirYd($sleeperStatsDto->getRecAirYd());
        $sleeperStatsEntity->setRecYpr($sleeperStatsDto->getRecYpr());
        $sleeperStatsEntity->setRec2029($sleeperStatsDto->getRec2029());
        $sleeperStatsEntity->setRec04($sleeperStatsDto->getRec04());
        $sleeperStatsEntity->setRec($sleeperStatsDto->getRec());
        $sleeperStatsEntity->setRec59($sleeperStatsDto->getRec59());
        $sleeperStatsEntity->setRecDrop($sleeperStatsDto->getRecDrop());
        $sleeperStatsEntity->setRecTd($sleeperStatsDto->getRecTd());
        $sleeperStatsEntity->setRecLng($sleeperStatsDto->getRecLng());
        $sleeperStatsEntity->setRec1019($sleeperStatsDto->getRec1019());
        $sleeperStatsEntity->setRecTgt($sleeperStatsDto->getRecTgt());
        $sleeperStatsEntity->setRecYar($sleeperStatsDto->getRecYar());
        $sleeperStatsEntity->setRec40p($sleeperStatsDto->getRec40p());
        $sleeperStatsEntity->setBonusRecWr($sleeperStatsDto->getBonusRecWr());
        $sleeperStatsEntity->setBonusRecYd100($sleeperStatsDto->getBonusRecYd100());
        $sleeperStatsEntity->setRecFd($sleeperStatsDto->getRecFd());
        $sleeperStatsEntity->setBonusRecRb($sleeperStatsDto->getBonusRecRb());
        $sleeperStatsEntity->setRecTd40p($sleeperStatsDto->getRecTd40p());
        $sleeperStatsEntity->setRec2pt($sleeperStatsDto->getRec2pt());
        $sleeperStatsEntity->setBonusRecTe($sleeperStatsDto->getBonusRecTe());

        $sleeperStatsEntity->setFumRec($sleeperStatsDto->getFumRec());
        $sleeperStatsEntity->setPassYd($sleeperStatsDto->getPassYd());
        $sleeperStatsEntity->setPassTd40p($sleeperStatsDto->getPassTd40p());
        $sleeperStatsEntity->setPassTd($sleeperStatsDto->getPassTd());
        $sleeperStatsEntity->setPassSack($sleeperStatsDto->getPassSack());
        $sleeperStatsEntity->setPassIntTd($sleeperStatsDto->getPassIntTd());
        $sleeperStatsEntity->setPassInt($sleeperStatsDto->getPassInt());
        $sleeperStatsEntity->setPassInc($sleeperStatsDto->getPassInc());
        $sleeperStatsEntity->setPassFd($sleeperStatsDto->getPassFd());
        $sleeperStatsEntity->setPassCmp40p($sleeperStatsDto->getPassCmp40p());
        $sleeperStatsEntity->setPassCmp($sleeperStatsDto->getPassCmp());
        $sleeperStatsEntity->setPassAtt($sleeperStatsDto->getPassAtt());
        $sleeperStatsEntity->setPass2Pt($sleeperStatsDto->getPass2Pt());

        $sleeperStatsEntity->setFum($sleeperStatsDto->getFum());
        $sleeperStatsEntity->setFumLost($sleeperStatsDto->getFumLost());
        $sleeperStatsEntity->setSack($sleeperStatsDto->getSack());
        $sleeperStatsEntity->setFf($sleeperStatsDto->getFf());
        $sleeperStatsEntity->setCmpPct($sleeperStatsDto->getCmpPct());
        $sleeperStatsEntity->setInt($sleeperStatsDto->getInt());

        $sleeperStatsEntity->setPtsStd($sleeperStatsDto->getPtsStd());
        $sleeperStatsEntity->setPtsHalfPpr($sleeperStatsDto->getPtsHalfPpr());
        $sleeperStatsEntity->setPtsPpr($sleeperStatsDto->getPtsPpr());
        $sleeperStatsEntity->setPtsAllow2127($sleeperStatsDto->getPtsAllow2127());
        $sleeperStatsEntity->setPtsAllow($sleeperStatsDto->getPtsAllow());

        $sleeperStatsEntity->setRankStd($sleeperStatsDto->getRankStd());
        $sleeperStatsEntity->setRankPpr($sleeperStatsDto->getRankPpr());
        $sleeperStatsEntity->setPosRankStd($sleeperStatsDto->getPosRankStd());
        $sleeperStatsEntity->setPosRankPpr($sleeperStatsDto->getPosRankPpr());

        $sleeperStatsEntity->setOffSnp($sleeperStatsDto->getOffSnp());
        $sleeperStatsEntity->setTmOffSnp($sleeperStatsDto->getTmOffSnp());
        $sleeperStatsEntity->setTmDefSnp($sleeperStatsDto->getTmDefSnp());
        $sleeperStatsEntity->setTmStSnp($sleeperStatsDto->getTmStSnp());

        $sleeperStatsEntity->setDefTd($sleeperStatsDto->getDefTd());
        $sleeperStatsEntity->setDefPrYd($sleeperStatsDto->getDefPrYd());
        $sleeperStatsEntity->setDefKrYd($sleeperStatsDto->getDefKrYd());
        $sleeperStatsEntity->setDefFumTd($sleeperStatsDto->getDefFumTd());

        $sleeperStatsEntity->setYdsAllow350399($sleeperStatsDto->getYdsAllow350399());
        $sleeperStatsEntity->setYdsAllow($sleeperStatsDto->getYdsAllow());

        $sleeperStatsEntity->setGs($sleeperStatsDto->getGs());
        $sleeperStatsEntity->setPenalty($sleeperStatsDto->getPenalty());
        $sleeperStatsEntity->setPenaltyYd($sleeperStatsDto->getPenaltyYd());

        $sleeperStatsEntity->setPosAdpDdPpr($sleeperStatsDto->getPosAdpDdPpr());
        $sleeperStatsEntity->setAdpDdPpr($sleeperStatsDto->getAdpDdPpr());
        $sleeperStatsEntity->setPr($sleeperStatsDto->getPr());
        $sleeperStatsEntity->setPrYd($sleeperStatsDto->getPrYd());
        $sleeperStatsEntity->setTklLoss($sleeperStatsDto->getTklLoss());
        $sleeperStatsEntity->setBlkKick($sleeperStatsDto->getBlkKick());

        return $sleeperStatsEntity;
    }
}
