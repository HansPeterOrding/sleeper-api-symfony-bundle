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

        // Games
        $sleeperStatsEntity->setGp($sleeperStatsDto->getGp());
        $sleeperStatsEntity->setGmsActive($sleeperStatsDto->getGmsActive());
        $sleeperStatsEntity->setGs($sleeperStatsDto->getGs());

        // Points
        $sleeperStatsEntity->setPtsStd($sleeperStatsDto->getPtsStd());
        $sleeperStatsEntity->setPtsHalfPpr($sleeperStatsDto->getPtsHalfPpr());
        $sleeperStatsEntity->setPtsPpr($sleeperStatsDto->getPtsPpr());

        // Ranks
        $sleeperStatsEntity->setRankStd($sleeperStatsDto->getRankStd());
        $sleeperStatsEntity->setRankPpr($sleeperStatsDto->getRankPpr());
        $sleeperStatsEntity->setPosRankStd($sleeperStatsDto->getPosRankStd());
        $sleeperStatsEntity->setPosRankPpr($sleeperStatsDto->getPosRankPpr());

        // Snap
        $sleeperStatsEntity->setOffSnp($sleeperStatsDto->getOffSnp());
        $sleeperStatsEntity->setTmOffSnp($sleeperStatsDto->getTmOffSnp());
        $sleeperStatsEntity->setTmDefSnp($sleeperStatsDto->getTmDefSnp());
        $sleeperStatsEntity->setTmStSnp($sleeperStatsDto->getTmStSnp());

        // Penalty
        $sleeperStatsEntity->setPenalty($sleeperStatsDto->getPenalty());
        $sleeperStatsEntity->setPenaltyYd($sleeperStatsDto->getPenaltyYd());

        // ADP
        $sleeperStatsEntity->setPosAdpDdPpr($sleeperStatsDto->getPosAdpDdPpr());
        $sleeperStatsEntity->setAdpDdPpr($sleeperStatsDto->getAdpDdPpr());

        // Passing
        $sleeperStatsEntity->setPassYd($sleeperStatsDto->getPassYd());
        $sleeperStatsEntity->setPassTd($sleeperStatsDto->getPassTd());
        $sleeperStatsEntity->setPassFd($sleeperStatsDto->getPassFd());
        $sleeperStatsEntity->setPass2pt($sleeperStatsDto->getPass2pt());
        $sleeperStatsEntity->setPassInt($sleeperStatsDto->getPassInt());
        $sleeperStatsEntity->setPassIntTd($sleeperStatsDto->getPassIntTd());
        $sleeperStatsEntity->setPassCmp($sleeperStatsDto->getPassCmp());
        $sleeperStatsEntity->setPassInc($sleeperStatsDto->getPassInc());
        $sleeperStatsEntity->setPassAtt($sleeperStatsDto->getPassAtt());
        $sleeperStatsEntity->setPassSack($sleeperStatsDto->getPassSack());
        $sleeperStatsEntity->setPassCmp40p($sleeperStatsDto->getPassCmp40p());
        $sleeperStatsEntity->setPassTd40p($sleeperStatsDto->getPassTd40p());
        $sleeperStatsEntity->setPassTd50p($sleeperStatsDto->getPassTd50p());
        $sleeperStatsEntity->setCmpPct($sleeperStatsDto->getCmpPct());

        // Rushing
        $sleeperStatsEntity->setRushYd($sleeperStatsDto->getRushYd());
        $sleeperStatsEntity->setRushTd($sleeperStatsDto->getRushTd());
        $sleeperStatsEntity->setRushFd($sleeperStatsDto->getRushFd());
        $sleeperStatsEntity->setRush2pt($sleeperStatsDto->getRush2pt());
        $sleeperStatsEntity->setRushAtt($sleeperStatsDto->getRushAtt());
        $sleeperStatsEntity->setRush40p($sleeperStatsDto->getRush40p());
        $sleeperStatsEntity->setRushTd40p($sleeperStatsDto->getRushTd40p());
        $sleeperStatsEntity->setRushTd50p($sleeperStatsDto->getRushTd50p());

        // Receiving
        $sleeperStatsEntity->setRec($sleeperStatsDto->getRec());
        $sleeperStatsEntity->setRecYd($sleeperStatsDto->getRecYd());
        $sleeperStatsEntity->setRecTd($sleeperStatsDto->getRecTd());
        $sleeperStatsEntity->setRecFd($sleeperStatsDto->getRecFd());
        $sleeperStatsEntity->setRec2pt($sleeperStatsDto->getRec2pt());
        $sleeperStatsEntity->setRec04($sleeperStatsDto->getRec04());
        $sleeperStatsEntity->setRec59($sleeperStatsDto->getRec59());
        $sleeperStatsEntity->setRec1019($sleeperStatsDto->getRec1019());
        $sleeperStatsEntity->setRec2029($sleeperStatsDto->getRec2029());
        $sleeperStatsEntity->setRec3039($sleeperStatsDto->getRec3039());
        $sleeperStatsEntity->setRec40p($sleeperStatsDto->getRec40p());
        $sleeperStatsEntity->setRecTd40p($sleeperStatsDto->getRecTd40p());
        $sleeperStatsEntity->setRecTd50p($sleeperStatsDto->getRecTd50p());
        $sleeperStatsEntity->setBonusRecRb($sleeperStatsDto->getBonusRecRb());
        $sleeperStatsEntity->setBonusRecWr($sleeperStatsDto->getBonusRecWr());
        $sleeperStatsEntity->setBonusRecTe($sleeperStatsDto->getBonusRecTe());
        $sleeperStatsEntity->setRecTdLng($sleeperStatsDto->getRecTdLng());
        $sleeperStatsEntity->setRecRzTgt($sleeperStatsDto->getRecRzTgt());
        $sleeperStatsEntity->setRecYpt($sleeperStatsDto->getRecYpt());
        $sleeperStatsEntity->setRecAirYd($sleeperStatsDto->getRecAirYd());
        $sleeperStatsEntity->setRecYpr($sleeperStatsDto->getRecYpr());
        $sleeperStatsEntity->setRecDrop($sleeperStatsDto->getRecDrop());
        $sleeperStatsEntity->setRecLng($sleeperStatsDto->getRecLng());
        $sleeperStatsEntity->setRecTgt($sleeperStatsDto->getRecTgt());
        $sleeperStatsEntity->setRecYar($sleeperStatsDto->getRecYar());

        // Kicking
        $sleeperStatsEntity->setFgm($sleeperStatsDto->getFgm());
        $sleeperStatsEntity->setFgm019($sleeperStatsDto->getFgm019());
        $sleeperStatsEntity->setFgm2029($sleeperStatsDto->getFgm2029());
        $sleeperStatsEntity->setFgm3039($sleeperStatsDto->getFgm3039());
        $sleeperStatsEntity->setFgm4049($sleeperStatsDto->getFgm4049());
        $sleeperStatsEntity->setFgm50p($sleeperStatsDto->getFgm50p());
        $sleeperStatsEntity->setFgmYds($sleeperStatsDto->getFgmYds());
        $sleeperStatsEntity->setFgmYdsOver30($sleeperStatsDto->getFgmYdsOver30());
        $sleeperStatsEntity->setXpm($sleeperStatsDto->getXpm());
        $sleeperStatsEntity->setFgmiss($sleeperStatsDto->getFgmiss());
        $sleeperStatsEntity->setFgmiss019($sleeperStatsDto->getFgmiss019());
        $sleeperStatsEntity->setFgmiss2029($sleeperStatsDto->getFgmiss2029());
        $sleeperStatsEntity->setFgmiss3039($sleeperStatsDto->getFgmiss3039());
        $sleeperStatsEntity->setFgmiss4049($sleeperStatsDto->getFgmiss4049());
        $sleeperStatsEntity->setFgmiss50p($sleeperStatsDto->getFgmiss50p());
        $sleeperStatsEntity->setXpmiss($sleeperStatsDto->getXpmiss());

        // Team defense
        $sleeperStatsEntity->setDefTd($sleeperStatsDto->getDefTd());
        $sleeperStatsEntity->setPtsAllow0($sleeperStatsDto->getPtsAllow0());
        $sleeperStatsEntity->setPtsAllow16($sleeperStatsDto->getPtsAllow16());
        $sleeperStatsEntity->setPtsAllow713($sleeperStatsDto->getPtsAllow713());
        $sleeperStatsEntity->setPtsAllow1420($sleeperStatsDto->getPtsAllow1420());
        $sleeperStatsEntity->setPtsAllow2127($sleeperStatsDto->getPtsAllow2127());
        $sleeperStatsEntity->setPtsAllow2834($sleeperStatsDto->getPtsAllow2834());
        $sleeperStatsEntity->setPtsAllow35p($sleeperStatsDto->getPtsAllow35p());
        $sleeperStatsEntity->setPtsAllow($sleeperStatsDto->getPtsAllow());
        $sleeperStatsEntity->setYdsAllow0100($sleeperStatsDto->getYdsAllow0100());
        $sleeperStatsEntity->setYdsAllow100199($sleeperStatsDto->getYdsAllow100199());
        $sleeperStatsEntity->setYdsAllow200299($sleeperStatsDto->getYdsAllow200299());
        $sleeperStatsEntity->setYdsAllow300349($sleeperStatsDto->getYdsAllow300349());
        $sleeperStatsEntity->setYdsAllow350399($sleeperStatsDto->getYdsAllow350399());
        $sleeperStatsEntity->setYdsAllow400449($sleeperStatsDto->getYdsAllow400449());
        $sleeperStatsEntity->setYdsAllow450499($sleeperStatsDto->getYdsAllow450499());
        $sleeperStatsEntity->setYdsAllow500549($sleeperStatsDto->getYdsAllow500549());
        $sleeperStatsEntity->setYdsAllow550p($sleeperStatsDto->getYdsAllow550p());
        $sleeperStatsEntity->setYdsAllow($sleeperStatsDto->getYdsAllow());
        $sleeperStatsEntity->setDef3AndOut($sleeperStatsDto->getDef3AndOut());
        $sleeperStatsEntity->setDef4AndStop($sleeperStatsDto->getDef4AndStop());
        $sleeperStatsEntity->setQbHit($sleeperStatsDto->getQbHit());
        $sleeperStatsEntity->setSack($sleeperStatsDto->getSack());
        $sleeperStatsEntity->setSackYd($sleeperStatsDto->getSackYd());
        $sleeperStatsEntity->setInt($sleeperStatsDto->getInt());
        $sleeperStatsEntity->setIntRetYd($sleeperStatsDto->getIntRetYd());
        $sleeperStatsEntity->setFumRec($sleeperStatsDto->getFumRec());
        $sleeperStatsEntity->setFumRetYd($sleeperStatsDto->getFumRetYd());
        $sleeperStatsEntity->setTklLoss($sleeperStatsDto->getTklLoss());
        $sleeperStatsEntity->setTklAst($sleeperStatsDto->getTklAst());
        $sleeperStatsEntity->setTklSolo($sleeperStatsDto->getTklSolo());
        $sleeperStatsEntity->setTkl($sleeperStatsDto->getTkl());
        $sleeperStatsEntity->setSafe($sleeperStatsDto->getSafe());
        $sleeperStatsEntity->setFf($sleeperStatsDto->getFf());
        $sleeperStatsEntity->setBlkKick($sleeperStatsDto->getBlkKick());
        $sleeperStatsEntity->setDefForcedPunts($sleeperStatsDto->getDefForcedPunts());
        $sleeperStatsEntity->setDefPassDef($sleeperStatsDto->getDefPassDef());
        $sleeperStatsEntity->setDef2pt($sleeperStatsDto->getDef2pt());
        $sleeperStatsEntity->setFgRetYd($sleeperStatsDto->getFgRetYd());
        $sleeperStatsEntity->setBlkKickRetYd($sleeperStatsDto->getBlkKickRetYd());
        $sleeperStatsEntity->setDefFumTd($sleeperStatsDto->getDefFumTd());

        // Special Teams Defense
        $sleeperStatsEntity->setDefStTd($sleeperStatsDto->getDefStTd());
        $sleeperStatsEntity->setDefStFf($sleeperStatsDto->getDefStFf());
        $sleeperStatsEntity->setDefStFumRec($sleeperStatsDto->getDefStFumRec());
        $sleeperStatsEntity->setDefStTklSolo($sleeperStatsDto->getDefStTklSolo());
        $sleeperStatsEntity->setDefPrYd($sleeperStatsDto->getDefPrYd());
        $sleeperStatsEntity->setDefKrYd($sleeperStatsDto->getDefKrYd());

        // Special Teams Player
        $sleeperStatsEntity->setStTd($sleeperStatsDto->getStTd());
        $sleeperStatsEntity->setStFf($sleeperStatsDto->getStFf());
        $sleeperStatsEntity->setStFumRec($sleeperStatsDto->getStFumRec());
        $sleeperStatsEntity->setStTklSolo($sleeperStatsDto->getStTklSolo());
        $sleeperStatsEntity->setPrYd($sleeperStatsDto->getPrYd());
        $sleeperStatsEntity->setKrYd($sleeperStatsDto->getKrYd());
        $sleeperStatsEntity->setPr($sleeperStatsDto->getPr());

        // Misc
        $sleeperStatsEntity->setFum($sleeperStatsDto->getFum());
        $sleeperStatsEntity->setFumLost($sleeperStatsDto->getFumLost());
        $sleeperStatsEntity->setFumRecTd($sleeperStatsDto->getFumRecTd());

        // Bonus
        $sleeperStatsEntity->setBonusRushYd100($sleeperStatsDto->getBonusRushYd100());
        $sleeperStatsEntity->setBonusRushYd200($sleeperStatsDto->getBonusRushYd200());
        $sleeperStatsEntity->setBonusRecYd100($sleeperStatsDto->getBonusRecYd100());
        $sleeperStatsEntity->setBonusRecYd200($sleeperStatsDto->getBonusRecYd200());
        $sleeperStatsEntity->setBonusPassYd300($sleeperStatsDto->getBonusPassYd300());
        $sleeperStatsEntity->setBonusPassYd400($sleeperStatsDto->getBonusPassYd400());
        $sleeperStatsEntity->setBonusRushRecYd100($sleeperStatsDto->getBonusRushRecYd100());
        $sleeperStatsEntity->setBonusRushRecYd200($sleeperStatsDto->getBonusRushRecYd200());
        $sleeperStatsEntity->setBonusPassCmp25($sleeperStatsDto->getBonusPassCmp25());
        $sleeperStatsEntity->setBonusRushAtt20($sleeperStatsDto->getBonusRushAtt20());
        $sleeperStatsEntity->setBonusFdRb($sleeperStatsDto->getBonusFdRb());
        $sleeperStatsEntity->setBonusFdWr($sleeperStatsDto->getBonusFdWr());
        $sleeperStatsEntity->setBonusFdTe($sleeperStatsDto->getBonusFdTe());
        $sleeperStatsEntity->setBonusFdQb($sleeperStatsDto->getBonusFdQb());

        // IDP
        $sleeperStatsEntity->setIdpDefTd($sleeperStatsDto->getIdpDefTd());
        $sleeperStatsEntity->setIdpSack($sleeperStatsDto->getIdpSack());
        $sleeperStatsEntity->setIdpSackYd($sleeperStatsDto->getIdpSackYd());
        $sleeperStatsEntity->setIdpQbHit($sleeperStatsDto->getIdpQbHit());
        $sleeperStatsEntity->setIdpTkl($sleeperStatsDto->getIdpTkl());
        $sleeperStatsEntity->setIdpTklLoss($sleeperStatsDto->getIdpTklLoss());
        $sleeperStatsEntity->setIdpBlkKick($sleeperStatsDto->getIdpBlkKick());
        $sleeperStatsEntity->setIdpInt($sleeperStatsDto->getIdpInt());
        $sleeperStatsEntity->setIdpIntRetYd($sleeperStatsDto->getIdpIntRetYd());
        $sleeperStatsEntity->setIdpFumRec($sleeperStatsDto->getIdpFumRec());
        $sleeperStatsEntity->setIdpFumRetYd($sleeperStatsDto->getIdpFumRetYd());
        $sleeperStatsEntity->setIdpFf($sleeperStatsDto->getIdpFf());
        $sleeperStatsEntity->setIdpSafe($sleeperStatsDto->getIdpSafe());
        $sleeperStatsEntity->setIdpTklAst($sleeperStatsDto->getIdpTklAst());
        $sleeperStatsEntity->setIdpTklSolo($sleeperStatsDto->getIdpTklSolo());
        $sleeperStatsEntity->setIdpPassDef($sleeperStatsDto->getIdpPassDef());
        $sleeperStatsEntity->setBonusTkl10p($sleeperStatsDto->getBonusTkl10p());
        $sleeperStatsEntity->setBonusSack2p($sleeperStatsDto->getBonusSack2p());
        $sleeperStatsEntity->setIdpPassDef3p($sleeperStatsDto->getIdpPassDef3p());
        $sleeperStatsEntity->setBonusDefIntTd50p($sleeperStatsDto->getBonusDefIntTd50p());
        $sleeperStatsEntity->setBonusDefFumTd50p($sleeperStatsDto->getBonusDefFumTd50p());

        return $sleeperStatsEntity;
    }
}
