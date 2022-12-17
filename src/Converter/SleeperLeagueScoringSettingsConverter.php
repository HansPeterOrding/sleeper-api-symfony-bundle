<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperLeagueScoringSettings as SleeperLeagueScoringSettingsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeagueScoringSettings as SleeperLeagueScoringSettingsEntity;

class SleeperLeagueScoringSettingsConverter
{
    public function toEntity(SleeperLeagueScoringSettingsDto $sleeperLeagueScoringSettingsDto, ?SleeperLeagueScoringSettingsEntity $sleeperLeagueScoringSettingsEntity = null): SleeperLeagueScoringSettingsEntity
    {
        if (!$sleeperLeagueScoringSettingsEntity) {
            $sleeperLeagueScoringSettingsEntity = new SleeperLeagueScoringSettingsEntity();
        }

        // Passing
        $sleeperLeagueScoringSettingsEntity->setPassYd($sleeperLeagueScoringSettingsDto->getPassYd());
        $sleeperLeagueScoringSettingsEntity->setPassTd($sleeperLeagueScoringSettingsDto->getPassTd());
        $sleeperLeagueScoringSettingsEntity->setPassFd($sleeperLeagueScoringSettingsDto->getPassFd());
        $sleeperLeagueScoringSettingsEntity->setPass2pt($sleeperLeagueScoringSettingsDto->getPass2pt());
        $sleeperLeagueScoringSettingsEntity->setPassInt($sleeperLeagueScoringSettingsDto->getPassInt());
        $sleeperLeagueScoringSettingsEntity->setPassIntTd($sleeperLeagueScoringSettingsDto->getPassIntTd());
        $sleeperLeagueScoringSettingsEntity->setPassCmp($sleeperLeagueScoringSettingsDto->getPassCmp());
        $sleeperLeagueScoringSettingsEntity->setPassInc($sleeperLeagueScoringSettingsDto->getPassInc());
        $sleeperLeagueScoringSettingsEntity->setPassAtt($sleeperLeagueScoringSettingsDto->getPassAtt());
        $sleeperLeagueScoringSettingsEntity->setPassSack($sleeperLeagueScoringSettingsDto->getPassSack());
        $sleeperLeagueScoringSettingsEntity->setPassCmp40p($sleeperLeagueScoringSettingsDto->getPassCmp40p());
        $sleeperLeagueScoringSettingsEntity->setPassTd40p($sleeperLeagueScoringSettingsDto->getPassTd40p());
        $sleeperLeagueScoringSettingsEntity->setPassTd50p($sleeperLeagueScoringSettingsDto->getPassTd50p());

        // Rushing
        $sleeperLeagueScoringSettingsEntity->setRushYd($sleeperLeagueScoringSettingsDto->getRushYd());
        $sleeperLeagueScoringSettingsEntity->setRushTd($sleeperLeagueScoringSettingsDto->getRushTd());
        $sleeperLeagueScoringSettingsEntity->setRushFd($sleeperLeagueScoringSettingsDto->getRushFd());
        $sleeperLeagueScoringSettingsEntity->setRush2pt($sleeperLeagueScoringSettingsDto->getRush2pt());
        $sleeperLeagueScoringSettingsEntity->setRushAtt($sleeperLeagueScoringSettingsDto->getRushAtt());
        $sleeperLeagueScoringSettingsEntity->setRush40p($sleeperLeagueScoringSettingsDto->getRush40p());
        $sleeperLeagueScoringSettingsEntity->setRushTd40p($sleeperLeagueScoringSettingsDto->getRushTd40p());
        $sleeperLeagueScoringSettingsEntity->setRushTd50p($sleeperLeagueScoringSettingsDto->getRushTd50p());

        // Receiving
        $sleeperLeagueScoringSettingsEntity->setRec($sleeperLeagueScoringSettingsDto->getRec());
        $sleeperLeagueScoringSettingsEntity->setRecYd($sleeperLeagueScoringSettingsDto->getRecYd());
        $sleeperLeagueScoringSettingsEntity->setRecTd($sleeperLeagueScoringSettingsDto->getRecTd());
        $sleeperLeagueScoringSettingsEntity->setRecFd($sleeperLeagueScoringSettingsDto->getRecFd());
        $sleeperLeagueScoringSettingsEntity->setRec2pt($sleeperLeagueScoringSettingsDto->getRec2pt());
        $sleeperLeagueScoringSettingsEntity->setRec04($sleeperLeagueScoringSettingsDto->getRec04());
        $sleeperLeagueScoringSettingsEntity->setRec59($sleeperLeagueScoringSettingsDto->getRec59());
        $sleeperLeagueScoringSettingsEntity->setRec1019($sleeperLeagueScoringSettingsDto->getRec1019());
        $sleeperLeagueScoringSettingsEntity->setRec2029($sleeperLeagueScoringSettingsDto->getRec2029());
        $sleeperLeagueScoringSettingsEntity->setRec3039($sleeperLeagueScoringSettingsDto->getRec3039());
        $sleeperLeagueScoringSettingsEntity->setRec40p($sleeperLeagueScoringSettingsDto->getRec40p());
        $sleeperLeagueScoringSettingsEntity->setRecTd40p($sleeperLeagueScoringSettingsDto->getRecTd40p());
        $sleeperLeagueScoringSettingsEntity->setRecTd50p($sleeperLeagueScoringSettingsDto->getRecTd50p());
        $sleeperLeagueScoringSettingsEntity->setBonusRecRb($sleeperLeagueScoringSettingsDto->getBonusRecRb());
        $sleeperLeagueScoringSettingsEntity->setBonusRecWr($sleeperLeagueScoringSettingsDto->getBonusRecWr());
        $sleeperLeagueScoringSettingsEntity->setBonusRecTe($sleeperLeagueScoringSettingsDto->getBonusRecTe());

        // Kicking
        $sleeperLeagueScoringSettingsEntity->setFgm($sleeperLeagueScoringSettingsDto->getFgm());
        $sleeperLeagueScoringSettingsEntity->setFgm019($sleeperLeagueScoringSettingsDto->getFgm019());
        $sleeperLeagueScoringSettingsEntity->setFgm2029($sleeperLeagueScoringSettingsDto->getFgm2029());
        $sleeperLeagueScoringSettingsEntity->setFgm3039($sleeperLeagueScoringSettingsDto->getFgm3039());
        $sleeperLeagueScoringSettingsEntity->setFgm4049($sleeperLeagueScoringSettingsDto->getFgm4049());
        $sleeperLeagueScoringSettingsEntity->setFgm50p($sleeperLeagueScoringSettingsDto->getFgm50p());
        $sleeperLeagueScoringSettingsEntity->setFgmYds($sleeperLeagueScoringSettingsDto->getFgmYds());
        $sleeperLeagueScoringSettingsEntity->setFgmYdsOver30($sleeperLeagueScoringSettingsDto->getFgmYdsOver30());
        $sleeperLeagueScoringSettingsEntity->setXpm($sleeperLeagueScoringSettingsDto->getXpm());
        $sleeperLeagueScoringSettingsEntity->setFgmiss($sleeperLeagueScoringSettingsDto->getFgmiss());
        $sleeperLeagueScoringSettingsEntity->setFgmiss019($sleeperLeagueScoringSettingsDto->getFgmiss019());
        $sleeperLeagueScoringSettingsEntity->setFgmiss2029($sleeperLeagueScoringSettingsDto->getFgmiss2029());
        $sleeperLeagueScoringSettingsEntity->setFgmiss3039($sleeperLeagueScoringSettingsDto->getFgmiss3039());
        $sleeperLeagueScoringSettingsEntity->setFgmiss4049($sleeperLeagueScoringSettingsDto->getFgmiss4049());
        $sleeperLeagueScoringSettingsEntity->setFgmiss50p($sleeperLeagueScoringSettingsDto->getFgmiss50p());
        $sleeperLeagueScoringSettingsEntity->setXpmiss($sleeperLeagueScoringSettingsDto->getXpmiss());

        // Team defense
        $sleeperLeagueScoringSettingsEntity->setDefTd($sleeperLeagueScoringSettingsDto->getDefTd());
        $sleeperLeagueScoringSettingsEntity->setPtsAllow0($sleeperLeagueScoringSettingsDto->getPtsAllow0());
        $sleeperLeagueScoringSettingsEntity->setPtsAllow16($sleeperLeagueScoringSettingsDto->getPtsAllow16());
        $sleeperLeagueScoringSettingsEntity->setPtsAllow713($sleeperLeagueScoringSettingsDto->getPtsAllow713());
        $sleeperLeagueScoringSettingsEntity->setPtsAllow1420($sleeperLeagueScoringSettingsDto->getPtsAllow1420());
        $sleeperLeagueScoringSettingsEntity->setPtsAllow2127($sleeperLeagueScoringSettingsDto->getPtsAllow2127());
        $sleeperLeagueScoringSettingsEntity->setPtsAllow2834($sleeperLeagueScoringSettingsDto->getPtsAllow2834());
        $sleeperLeagueScoringSettingsEntity->setPtsAllow35p($sleeperLeagueScoringSettingsDto->getPtsAllow35p());
        $sleeperLeagueScoringSettingsEntity->setPtsAllow($sleeperLeagueScoringSettingsDto->getPtsAllow());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow0100($sleeperLeagueScoringSettingsDto->getYdsAllow0100());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow100199($sleeperLeagueScoringSettingsDto->getYdsAllow100199());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow200299($sleeperLeagueScoringSettingsDto->getYdsAllow200299());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow300349($sleeperLeagueScoringSettingsDto->getYdsAllow300349());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow350399($sleeperLeagueScoringSettingsDto->getYdsAllow350399());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow400449($sleeperLeagueScoringSettingsDto->getYdsAllow400449());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow450499($sleeperLeagueScoringSettingsDto->getYdsAllow450499());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow500549($sleeperLeagueScoringSettingsDto->getYdsAllow500549());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow550p($sleeperLeagueScoringSettingsDto->getYdsAllow550p());
        $sleeperLeagueScoringSettingsEntity->setYdsAllow($sleeperLeagueScoringSettingsDto->getYdsAllow());
        $sleeperLeagueScoringSettingsEntity->setDef3AndOut($sleeperLeagueScoringSettingsDto->getDef3AndOut());
        $sleeperLeagueScoringSettingsEntity->setDef4AndStop($sleeperLeagueScoringSettingsDto->getDef4AndStop());
        $sleeperLeagueScoringSettingsEntity->setQbHit($sleeperLeagueScoringSettingsDto->getQbHit());
        $sleeperLeagueScoringSettingsEntity->setSack($sleeperLeagueScoringSettingsDto->getSack());
        $sleeperLeagueScoringSettingsEntity->setSackYd($sleeperLeagueScoringSettingsDto->getSackYd());
        $sleeperLeagueScoringSettingsEntity->setInt($sleeperLeagueScoringSettingsDto->getInt());
        $sleeperLeagueScoringSettingsEntity->setIntRetYd($sleeperLeagueScoringSettingsDto->getIntRetYd());
        $sleeperLeagueScoringSettingsEntity->setFumRec($sleeperLeagueScoringSettingsDto->getFumRec());
        $sleeperLeagueScoringSettingsEntity->setFumRetYd($sleeperLeagueScoringSettingsDto->getFumRetYd());
        $sleeperLeagueScoringSettingsEntity->setTklLoss($sleeperLeagueScoringSettingsDto->getTklLoss());
        $sleeperLeagueScoringSettingsEntity->setTklAst($sleeperLeagueScoringSettingsDto->getTklAst());
        $sleeperLeagueScoringSettingsEntity->setTklSolo($sleeperLeagueScoringSettingsDto->getTklSolo());
        $sleeperLeagueScoringSettingsEntity->setTkl($sleeperLeagueScoringSettingsDto->getTkl());
        $sleeperLeagueScoringSettingsEntity->setSafe($sleeperLeagueScoringSettingsDto->getSafe());
        $sleeperLeagueScoringSettingsEntity->setFf($sleeperLeagueScoringSettingsDto->getFf());
        $sleeperLeagueScoringSettingsEntity->setBlkKick($sleeperLeagueScoringSettingsDto->getBlkKick());
        $sleeperLeagueScoringSettingsEntity->setDefForcedPunts($sleeperLeagueScoringSettingsDto->getDefForcedPunts());
        $sleeperLeagueScoringSettingsEntity->setDefPassDef($sleeperLeagueScoringSettingsDto->getDefPassDef());
        $sleeperLeagueScoringSettingsEntity->setDef2pt($sleeperLeagueScoringSettingsDto->getDef2pt());
        $sleeperLeagueScoringSettingsEntity->setFgRetYd($sleeperLeagueScoringSettingsDto->getFgRetYd());
        $sleeperLeagueScoringSettingsEntity->setBlkKickRetYd($sleeperLeagueScoringSettingsDto->getBlkKickRetYd());

        // Special Teams Defense
        $sleeperLeagueScoringSettingsEntity->setDefStTd($sleeperLeagueScoringSettingsDto->getDefStTd());
        $sleeperLeagueScoringSettingsEntity->setDefStFf($sleeperLeagueScoringSettingsDto->getDefStFf());
        $sleeperLeagueScoringSettingsEntity->setDefStFumRec($sleeperLeagueScoringSettingsDto->getDefStFumRec());
        $sleeperLeagueScoringSettingsEntity->setDefStTklSolo($sleeperLeagueScoringSettingsDto->getDefStTklSolo());
        $sleeperLeagueScoringSettingsEntity->setDefPrYd($sleeperLeagueScoringSettingsDto->getDefPrYd());
        $sleeperLeagueScoringSettingsEntity->setDefKrYd($sleeperLeagueScoringSettingsDto->getDefKrYd());

        // Special Teams Player
        $sleeperLeagueScoringSettingsEntity->setStTd($sleeperLeagueScoringSettingsDto->getStTd());
        $sleeperLeagueScoringSettingsEntity->setStFf($sleeperLeagueScoringSettingsDto->getStFf());
        $sleeperLeagueScoringSettingsEntity->setStFumRec($sleeperLeagueScoringSettingsDto->getStFumRec());
        $sleeperLeagueScoringSettingsEntity->setStTklSolo($sleeperLeagueScoringSettingsDto->getStTklSolo());
        $sleeperLeagueScoringSettingsEntity->setPrYd($sleeperLeagueScoringSettingsDto->getPrYd());
        $sleeperLeagueScoringSettingsEntity->setKrYd($sleeperLeagueScoringSettingsDto->getKrYd());

        // Misc
        $sleeperLeagueScoringSettingsEntity->setFum($sleeperLeagueScoringSettingsDto->getFum());
        $sleeperLeagueScoringSettingsEntity->setFumLost($sleeperLeagueScoringSettingsDto->getFumLost());
        $sleeperLeagueScoringSettingsEntity->setFumRecTd($sleeperLeagueScoringSettingsDto->getFumRecTd());

        // Bonus
        $sleeperLeagueScoringSettingsEntity->setBonusRushYd100($sleeperLeagueScoringSettingsDto->getBonusRushYd100());
        $sleeperLeagueScoringSettingsEntity->setBonusRushYd200($sleeperLeagueScoringSettingsDto->getBonusRushYd200());
        $sleeperLeagueScoringSettingsEntity->setBonusRecYd100($sleeperLeagueScoringSettingsDto->getBonusRecYd100());
        $sleeperLeagueScoringSettingsEntity->setBonusRecYd200($sleeperLeagueScoringSettingsDto->getBonusRecYd200());
        $sleeperLeagueScoringSettingsEntity->setBonusPassYd300($sleeperLeagueScoringSettingsDto->getBonusPassYd300());
        $sleeperLeagueScoringSettingsEntity->setBonusPassYd400($sleeperLeagueScoringSettingsDto->getBonusPassYd400());
        $sleeperLeagueScoringSettingsEntity->setBonusRushRecYd100($sleeperLeagueScoringSettingsDto->getBonusRushRecYd100());
        $sleeperLeagueScoringSettingsEntity->setBonusRushRecYd200($sleeperLeagueScoringSettingsDto->getBonusRushRecYd200());
        $sleeperLeagueScoringSettingsEntity->setBonusPassCmp25($sleeperLeagueScoringSettingsDto->getBonusPassCmp25());
        $sleeperLeagueScoringSettingsEntity->setBonusRushAtt20($sleeperLeagueScoringSettingsDto->getBonusRushAtt20());
        $sleeperLeagueScoringSettingsEntity->setBonusFdRb($sleeperLeagueScoringSettingsDto->getBonusFdRb());
        $sleeperLeagueScoringSettingsEntity->setBonusFdWr($sleeperLeagueScoringSettingsDto->getBonusFdWr());
        $sleeperLeagueScoringSettingsEntity->setBonusFdTe($sleeperLeagueScoringSettingsDto->getBonusFdTe());
        $sleeperLeagueScoringSettingsEntity->setBonusFdQb($sleeperLeagueScoringSettingsDto->getBonusFdQb());

        // IDP
        $sleeperLeagueScoringSettingsEntity->setIdpDefTd($sleeperLeagueScoringSettingsDto->getIdpDefTd());
        $sleeperLeagueScoringSettingsEntity->setIdpSack($sleeperLeagueScoringSettingsDto->getIdpSack());
        $sleeperLeagueScoringSettingsEntity->setIdpSackYd($sleeperLeagueScoringSettingsDto->getIdpSackYd());
        $sleeperLeagueScoringSettingsEntity->setIdpQbHit($sleeperLeagueScoringSettingsDto->getIdpQbHit());
        $sleeperLeagueScoringSettingsEntity->setIdpTkl($sleeperLeagueScoringSettingsDto->getIdpTkl());
        $sleeperLeagueScoringSettingsEntity->setIdpTklLoss($sleeperLeagueScoringSettingsDto->getIdpTklLoss());
        $sleeperLeagueScoringSettingsEntity->setIdpBlkKick($sleeperLeagueScoringSettingsDto->getIdpBlkKick());
        $sleeperLeagueScoringSettingsEntity->setIdpInt($sleeperLeagueScoringSettingsDto->getIdpInt());
        $sleeperLeagueScoringSettingsEntity->setIdpIntRetYd($sleeperLeagueScoringSettingsDto->getIdpIntRetYd());
        $sleeperLeagueScoringSettingsEntity->setIdpFumRec($sleeperLeagueScoringSettingsDto->getIdpFumRec());
        $sleeperLeagueScoringSettingsEntity->setIdpFumRetYd($sleeperLeagueScoringSettingsDto->getIdpFumRetYd());
        $sleeperLeagueScoringSettingsEntity->setIdpFf($sleeperLeagueScoringSettingsDto->getIdpFf());
        $sleeperLeagueScoringSettingsEntity->setIdpSafe($sleeperLeagueScoringSettingsDto->getIdpSafe());
        $sleeperLeagueScoringSettingsEntity->setIdpTklAst($sleeperLeagueScoringSettingsDto->getIdpTklAst());
        $sleeperLeagueScoringSettingsEntity->setIdpTklSolo($sleeperLeagueScoringSettingsDto->getIdpTklSolo());
        $sleeperLeagueScoringSettingsEntity->setIdpPassDef($sleeperLeagueScoringSettingsDto->getIdpPassDef());
        $sleeperLeagueScoringSettingsEntity->setBonusTkl10p($sleeperLeagueScoringSettingsDto->getBonusTkl10p());
        $sleeperLeagueScoringSettingsEntity->setBonusSack2p($sleeperLeagueScoringSettingsDto->getBonusSack2p());
        $sleeperLeagueScoringSettingsEntity->setIdpPassDef3p($sleeperLeagueScoringSettingsDto->getIdpPassDef3p());
        $sleeperLeagueScoringSettingsEntity->setBonusDefIntTd50p($sleeperLeagueScoringSettingsDto->getBonusDefIntTd50p());
        $sleeperLeagueScoringSettingsEntity->setBonusDefFumTd50p($sleeperLeagueScoringSettingsDto->getBonusDefFumTd50p());

        return $sleeperLeagueScoringSettingsEntity;
    }
}
