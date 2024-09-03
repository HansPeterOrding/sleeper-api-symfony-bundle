<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperTransactionMetadata as SleeperTransactionMetadataDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransactionMetadata as SleeperTransactionMetadataEntity;

class SleeperTransactionMetadataConverter {
    public function toEntity(
        SleeperTransactionMetadataDto     $sleeperTransactionMetadataDto,
        ?SleeperTransactionMetadataEntity $sleeperTransactionMetadataEntity
    ): SleeperTransactionMetadataEntity
    {
        if (!$sleeperTransactionMetadataEntity) {
            $sleeperTransactionMetadataEntity = new SleeperTransactionMetadataEntity();
        }

        $sleeperTransactionMetadataEntity->setNotes($sleeperTransactionMetadataDto->getNotes());

        return $sleeperTransactionMetadataEntity;
    }
}
