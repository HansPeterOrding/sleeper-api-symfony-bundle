<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperDraftConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;

/**
 * @property SleeperDraftConverter $converter
 */
class SleeperDraftImporter extends AbstractImporter
{
    public function import(string $sleeperDraftId, ?SleeperLeague $sleeperLeague = null): SleeperDraft
    {
        $sleeperDraft = $this->sleeperApiClient->draft()->get($sleeperDraftId);

        if(!$sleeperDraft) {
            throw new ImportException(sprintf('Draft with sleeperDraftId %s not found', $sleeperDraftId));
        }

        $entity = $this->converter->toEntity($sleeperDraft);
        $entity->setLeague($sleeperLeague);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
