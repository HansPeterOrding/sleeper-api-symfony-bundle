<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayoffMatchupSource as SleeperPlayoffMatchupSourceDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchupSource as SleeperPlayoffMatchupSourceEntity;

class SleeperPlayoffMatchupSourceConverter implements ConverterInterface {
    public function toEntity(
        ?SleeperPlayoffMatchupSourceDto    $sleeperPlayoffMatchupSourceDto = null,
        ?SleeperPlayoffMatchupSourceEntity $sleeperPlayoffMatchupSourceEntity = null
    ): ?SleeperPlayoffMatchupSourceEntity
    {
        if (null === $sleeperPlayoffMatchupSourceDto) {
            return null;
        }

        if (!$sleeperPlayoffMatchupSourceEntity) {
            $sleeperPlayoffMatchupSourceEntity = new SleeperPlayoffMatchupSourceEntity();
        }

        $sleeperPlayoffMatchupSourceEntity->setW($sleeperPlayoffMatchupSourceDto->getW());
        $sleeperPlayoffMatchupSourceEntity->setL($sleeperPlayoffMatchupSourceDto->getL());

        return $sleeperPlayoffMatchupSourceEntity;
    }
}
