<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperStats as SleeperStatsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperStats as SleeperStatsEntity;

class SleeperStatsConverter implements ConverterInterface {
    public function toEntity(
        SleeperStatsDto     $sleeperStatsDto,
        ?SleeperStatsEntity $sleeperStatsEntity = null
    ): SleeperStatsEntity
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
        $sleeperStatsEntity->setPtsIdp($sleeperStatsDto->getPtsIdp());

        // Ranks
        $sleeperStatsEntity->setRankStd($sleeperStatsDto->getRankStd());
        $sleeperStatsEntity->setRankPpr($sleeperStatsDto->getRankPpr());
        $sleeperStatsEntity->setRankHalfPpr($sleeperStatsDto->getRankHalfPpr());
        $sleeperStatsEntity->setPosRankStd($sleeperStatsDto->getPosRankStd());
        $sleeperStatsEntity->setPosRankPpr($sleeperStatsDto->getPosRankPpr());
        $sleeperStatsEntity->setPosRankHalfPpr($sleeperStatsDto->getPosRankHalfPpr());

        // Snap
        $sleeperStatsEntity->setOffSnp($sleeperStatsDto->getOffSnp());
        $sleeperStatsEntity->setDefSnp($sleeperStatsDto->getDefSnp());
        $sleeperStatsEntity->setTmOffSnp($sleeperStatsDto->getTmOffSnp());
        $sleeperStatsEntity->setTmDefSnp($sleeperStatsDto->getTmDefSnp());
        $sleeperStatsEntity->setTmStSnp($sleeperStatsDto->getTmStSnp());
        $sleeperStatsEntity->setStSnp($sleeperStatsDto->getStSnp());

        // Penalty
        $sleeperStatsEntity->setPenalty($sleeperStatsDto->getPenalty());
        $sleeperStatsEntity->setPenaltyYd($sleeperStatsDto->getPenaltyYd());

        // ADP
        $sleeperStatsEntity->setPosAdpDdPpr($sleeperStatsDto->getPosAdpDdPpr());
        $sleeperStatsEntity->setAdpDdPpr($sleeperStatsDto->getAdpDdPpr());

        // Passing
        $sleeperStatsEntity->setPassYd($sleeperStatsDto->getPassYd());
        $sleeperStatsEntity->setPassYpa($sleeperStatsDto->getPassYpa());
        $sleeperStatsEntity->setPassYpc($sleeperStatsDto->getPassYpc());
        $sleeperStatsEntity->setPassAirYd($sleeperStatsDto->getPassAirYd());
        $sleeperStatsEntity->setPassLng($sleeperStatsDto->getPassLng());
        $sleeperStatsEntity->setPassRtg($sleeperStatsDto->getPassRtg());
        $sleeperStatsEntity->setPassRushYd($sleeperStatsDto->getPassRushYd());
        $sleeperStatsEntity->setPassRzAtt($sleeperStatsDto->getPassRzAtt());
        $sleeperStatsEntity->setPassTd($sleeperStatsDto->getPassTd());
        $sleeperStatsEntity->setPassFd($sleeperStatsDto->getPassFd());
        $sleeperStatsEntity->setPass2pt($sleeperStatsDto->getPass2pt());
        $sleeperStatsEntity->setPassInt($sleeperStatsDto->getPassInt());
        $sleeperStatsEntity->setPassIntTd($sleeperStatsDto->getPassIntTd());
        $sleeperStatsEntity->setPassCmp($sleeperStatsDto->getPassCmp());
        $sleeperStatsEntity->setPassInc($sleeperStatsDto->getPassInc());
        $sleeperStatsEntity->setPassAtt($sleeperStatsDto->getPassAtt());
        $sleeperStatsEntity->setPassSack($sleeperStatsDto->getPassSack());
        $sleeperStatsEntity->setPassSackYds($sleeperStatsDto->getPassSackYds());
        $sleeperStatsEntity->setPassCmp40p($sleeperStatsDto->getPassCmp40p());
        $sleeperStatsEntity->setPassTd40p($sleeperStatsDto->getPassTd40p());
        $sleeperStatsEntity->setPassTd50p($sleeperStatsDto->getPassTd50p());
        $sleeperStatsEntity->setPassTdLng($sleeperStatsDto->getPassTdLng());
        $sleeperStatsEntity->setCmpPct($sleeperStatsDto->getCmpPct());

        // Rushing
        $sleeperStatsEntity->setRushYd($sleeperStatsDto->getRushYd());
        $sleeperStatsEntity->setRushTd($sleeperStatsDto->getRushTd());
        $sleeperStatsEntity->setRushTdLng($sleeperStatsDto->getRushTdLng());
        $sleeperStatsEntity->setRushFd($sleeperStatsDto->getRushFd());
        $sleeperStatsEntity->setRush2pt($sleeperStatsDto->getRush2pt());
        $sleeperStatsEntity->setRushAtt($sleeperStatsDto->getRushAtt());
        $sleeperStatsEntity->setRushBtkl($sleeperStatsDto->getRushBtkl());
        $sleeperStatsEntity->setRushLng($sleeperStatsDto->getRushLng());
        $sleeperStatsEntity->setRushRecYd($sleeperStatsDto->getRushRecYd());
        $sleeperStatsEntity->setRushRzAtt($sleeperStatsDto->getRushRzAtt());
        $sleeperStatsEntity->setRush40p($sleeperStatsDto->getRush40p());
        $sleeperStatsEntity->setRushTd40p($sleeperStatsDto->getRushTd40p());
        $sleeperStatsEntity->setRushTd50p($sleeperStatsDto->getRushTd50p());
        $sleeperStatsEntity->setRushTklLoss($sleeperStatsDto->getRushTklLoss());
        $sleeperStatsEntity->setRushTklLossYd($sleeperStatsDto->getRushTklLossYd());
        $sleeperStatsEntity->setRushYac($sleeperStatsDto->getRushYac());
        $sleeperStatsEntity->setRushYpa($sleeperStatsDto->getRushYpa());

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
        $sleeperStatsEntity->setFga($sleeperStatsDto->getFga());
        $sleeperStatsEntity->setFgm($sleeperStatsDto->getFgm());
        $sleeperStatsEntity->setFgm019($sleeperStatsDto->getFgm019());
        $sleeperStatsEntity->setFgm2029($sleeperStatsDto->getFgm2029());
        $sleeperStatsEntity->setFgm3039($sleeperStatsDto->getFgm3039());
        $sleeperStatsEntity->setFgm4049($sleeperStatsDto->getFgm4049());
        $sleeperStatsEntity->setFgm50p($sleeperStatsDto->getFgm50p());
        $sleeperStatsEntity->setFgm5059($sleeperStatsDto->getFgm5059());
        $sleeperStatsEntity->setFgm60p($sleeperStatsDto->getFgm60p());
        $sleeperStatsEntity->setFgmLng($sleeperStatsDto->getFgmLng());
        $sleeperStatsEntity->setFgmPct($sleeperStatsDto->getFgmPct());
        $sleeperStatsEntity->setFgmYds($sleeperStatsDto->getFgmYds());
        $sleeperStatsEntity->setFgmYdsOver30($sleeperStatsDto->getFgmYdsOver30());
        $sleeperStatsEntity->setXpm($sleeperStatsDto->getXpm());
        $sleeperStatsEntity->setXpBlkd($sleeperStatsDto->getXpBlkd());
        $sleeperStatsEntity->setXpa($sleeperStatsDto->getXpa());
        $sleeperStatsEntity->setFgmiss($sleeperStatsDto->getFgmiss());
        $sleeperStatsEntity->setFgmiss019($sleeperStatsDto->getFgmiss019());
        $sleeperStatsEntity->setFgmiss2029($sleeperStatsDto->getFgmiss2029());
        $sleeperStatsEntity->setFgmiss3039($sleeperStatsDto->getFgmiss3039());
        $sleeperStatsEntity->setFgmiss4049($sleeperStatsDto->getFgmiss4049());
        $sleeperStatsEntity->setFgmiss50p($sleeperStatsDto->getFgmiss50p());
        $sleeperStatsEntity->setFgmiss5059($sleeperStatsDto->getFgmiss5059());
        $sleeperStatsEntity->setFgmiss60p($sleeperStatsDto->getFgmiss60p());
        $sleeperStatsEntity->setFgBlkd($sleeperStatsDto->getFgBlkd());
        $sleeperStatsEntity->setXpmiss($sleeperStatsDto->getXpmiss());
        $sleeperStatsEntity->setPuntNetYd($sleeperStatsDto->getPuntNetYd());
        $sleeperStatsEntity->setPuntYds($sleeperStatsDto->getPuntYds());
        $sleeperStatsEntity->setPunts($sleeperStatsDto->getPunts());

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
        $sleeperStatsEntity->setFumRecEzTds($sleeperStatsDto->getFumRecEzTds());
        $sleeperStatsEntity->setMiscRetYd($sleeperStatsDto->getMiscRetYd());
        $sleeperStatsEntity->setTklLoss($sleeperStatsDto->getTklLoss());
        $sleeperStatsEntity->setTklAst($sleeperStatsDto->getTklAst());
        $sleeperStatsEntity->setTklAstMisc($sleeperStatsDto->getTklAstMisc());
        $sleeperStatsEntity->setTklSolo($sleeperStatsDto->getTklSolo());
        $sleeperStatsEntity->setTklSoloMisc($sleeperStatsDto->getTklSoloMisc());
        $sleeperStatsEntity->setTkl($sleeperStatsDto->getTkl());
        $sleeperStatsEntity->setSafe($sleeperStatsDto->getSafe());
        $sleeperStatsEntity->setFf($sleeperStatsDto->getFf());
        $sleeperStatsEntity->setFfMisc($sleeperStatsDto->getFfMisc());
        $sleeperStatsEntity->setBlkKick($sleeperStatsDto->getBlkKick());
        $sleeperStatsEntity->setDefForcedPunts($sleeperStatsDto->getDefForcedPunts());
        $sleeperStatsEntity->setDefPassDef($sleeperStatsDto->getDefPassDef());
        $sleeperStatsEntity->setDef2pt($sleeperStatsDto->getDef2pt());
        $sleeperStatsEntity->setFgRetYd($sleeperStatsDto->getFgRetYd());
        $sleeperStatsEntity->setBlkKickRetYd($sleeperStatsDto->getBlkKickRetYd());
        $sleeperStatsEntity->setDefFumTd($sleeperStatsDto->getDefFumTd());
        $sleeperStatsEntity->setKickPts($sleeperStatsDto->getKickPts());

        // Special Teams Defense
        $sleeperStatsEntity->setDefStTd($sleeperStatsDto->getDefStTd());
        $sleeperStatsEntity->setDefStFf($sleeperStatsDto->getDefStFf());
        $sleeperStatsEntity->setDefStFumRec($sleeperStatsDto->getDefStFumRec());
        $sleeperStatsEntity->setDefStTklSolo($sleeperStatsDto->getDefStTklSolo());
        $sleeperStatsEntity->setDefPr($sleeperStatsDto->getDefPr());
        $sleeperStatsEntity->setDefPrTd($sleeperStatsDto->getDefPrTd());
        $sleeperStatsEntity->setDefPrYd($sleeperStatsDto->getDefPrYd());
        $sleeperStatsEntity->setDefPrLng($sleeperStatsDto->getDefPrLng());
        $sleeperStatsEntity->setDefPrYpa($sleeperStatsDto->getDefPrYpa());
        $sleeperStatsEntity->setDefKr($sleeperStatsDto->getDefKr());
        $sleeperStatsEntity->setDefKrTd($sleeperStatsDto->getDefKrTd());
        $sleeperStatsEntity->setDefKrYd($sleeperStatsDto->getDefKrYd());
        $sleeperStatsEntity->setDefKrLng($sleeperStatsDto->getDefKrLng());
        $sleeperStatsEntity->setDefKrYpa($sleeperStatsDto->getDefKrYpa());

        // Special Teams Player
        $sleeperStatsEntity->setStTd($sleeperStatsDto->getStTd());
        $sleeperStatsEntity->setStFf($sleeperStatsDto->getStFf());
        $sleeperStatsEntity->setStFumRec($sleeperStatsDto->getStFumRec());
        $sleeperStatsEntity->setStTklSolo($sleeperStatsDto->getStTklSolo());
        $sleeperStatsEntity->setPr($sleeperStatsDto->getPr());
        $sleeperStatsEntity->setPrTd($sleeperStatsDto->getPrTd());
        $sleeperStatsEntity->setPrYd($sleeperStatsDto->getPrYd());
        $sleeperStatsEntity->setPrLng($sleeperStatsDto->getPrLng());
        $sleeperStatsEntity->setPrYpa($sleeperStatsDto->getPrYpa());
        $sleeperStatsEntity->setKr($sleeperStatsEntity->getKr());
        $sleeperStatsEntity->setKrTd($sleeperStatsEntity->getKrTd());
        $sleeperStatsEntity->setKrYd($sleeperStatsDto->getKrYd());
        $sleeperStatsEntity->setKrLng($sleeperStatsDto->getKrLng());
        $sleeperStatsEntity->setKrYpa($sleeperStatsDto->getKrYpa());

        // Misc
        $sleeperStatsEntity->setTd($sleeperStatsDto->getTd());
        $sleeperStatsEntity->setMiscTd($sleeperStatsDto->getMiscTd());
        $sleeperStatsEntity->setFum($sleeperStatsDto->getFum());
        $sleeperStatsEntity->setFumLost($sleeperStatsDto->getFumLost());
        $sleeperStatsEntity->setFumRecTd($sleeperStatsDto->getFumRecTd());
        $sleeperStatsEntity->setAnytimeTds($sleeperStatsDto->getAnytimeTds());
        $sleeperStatsEntity->setFirstTd($sleeperStatsDto->getFirstTd());

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

        // Fantasy Points allowed
        $sleeperStatsEntity->setFanPtsAllow($sleeperStatsDto->getFanPtsAllow());
        $sleeperStatsEntity->setFanPtsAllowDef($sleeperStatsDto->getFanPtsAllowDef());
        $sleeperStatsEntity->setFanPtsAllowK($sleeperStatsDto->getFanPtsAllowK());
        $sleeperStatsEntity->setFanPtsAllowQb($sleeperStatsDto->getFanPtsAllowQb());
        $sleeperStatsEntity->setFanPtsAllowRb($sleeperStatsDto->getFanPtsAllowRb());
        $sleeperStatsEntity->setFanPtsAllowTe($sleeperStatsDto->getFanPtsAllowTe());
        $sleeperStatsEntity->setFanPtsAllowWr($sleeperStatsDto->getFanPtsAllowWr());

        return $sleeperStatsEntity;
    }
}
