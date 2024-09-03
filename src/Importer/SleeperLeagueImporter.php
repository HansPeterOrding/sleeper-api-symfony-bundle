<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientFactory;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperLeagueConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Exception\ImportException;

/**
 * @property SleeperLeagueConverter $converter
 */
class SleeperLeagueImporter extends AbstractImporter
{
    public function import(string $sleeperLeagueId): SleeperLeague
    {
        $sleeperLeague = $this->sleeperApiClient->league()->get($sleeperLeagueId);

        if(!$sleeperLeague) {
            throw new ImportException(sprintf('League with sleeperLeagueId %s not found', $sleeperLeagueId));
        }

        $entity = $this->converter->toEntity($sleeperLeague);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }
}
