<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftMetadata as SleeperDraftMetadataDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\ScoringTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftMetadata as SleeperDraftMetadataEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperDraftMetadataRepository;

class SleeperDraftMetadataConverter {
    public function toEntity(
        SleeperDraftMetadataDto     $sleeperDraftMetadataDto,
        ?SleeperDraftMetadataEntity $sleeperDraftMetadataEntity
    ): SleeperDraftMetadataEntity
    {
        if (!$sleeperDraftMetadataEntity) {
            $sleeperDraftMetadataEntity = new SleeperDraftMetadataEntity();
        }

        // tryFrom(), not from(). Sleeper introduces scoring types without notice
        // and publishes no schema, so an unrecognised value is an expected input,
        // not an exception. from() turned one unknown string into a permanent
        // league-sync failure that burned all five Messenger retries.
        // Unknown or absent -> null; the league syncs, minus this one field.
        $rawScoringType = $sleeperDraftMetadataDto->getScoringType();
        $sleeperDraftMetadataEntity->setScoringType(
            null !== $rawScoringType ? ScoringTypeEnum::tryFrom($rawScoringType) : null
        );
        $sleeperDraftMetadataEntity->setName($sleeperDraftMetadataDto->getName());
        $sleeperDraftMetadataEntity->setElapsedPickTimer($sleeperDraftMetadataDto->getElapsedPickTimer());
        $sleeperDraftMetadataEntity->setDescription($sleeperDraftMetadataDto->getDescription());

        return $sleeperDraftMetadataEntity;
    }
}
