<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperNflStateConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperNflState;
use HansPeterOrding\SleeperApiSymfonyBundle\Exception\ImportException;

/**
 * @property SleeperNflStateConverter $converter
 */
class SleeperNflStateImporter extends AbstractImporter {
    public function import(): SleeperNflState
    {
        $nflState = $this->sleeperApiClient->nflState()->get();

        $entity = $this->converter->toEntity($nflState);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
