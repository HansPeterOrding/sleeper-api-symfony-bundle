<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperDraftConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperNflState;
use HansPeterOrding\SleeperApiSymfonyBundle\Exception\ImportException;

/**
 * @property SleeperDraftConverter $converter
 */
class SleeperNflStateImporter extends AbstractImporter {
    public function import(): SleeperNflState
    {
        $nflState = $this->sleeperApiClient->nflState()->get();

        if (!$nflState) {
            throw new ImportException('NflState not found');
        }

        $entity = $this->converter->toEntity($nflState);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
