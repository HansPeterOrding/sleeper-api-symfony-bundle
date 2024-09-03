<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperRosterConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser;

/**
 * @property SleeperRosterConverter $converter
 */
class SleeperRosterImporter extends AbstractImporter {
    /**
     * @param SleeperUser[] $sleeperUsers
     *
     * @return SleeperRoster[]
     */
    public function importLeagueRosters(string $sleeperLeagueId, array $sleeperUsers, ?SleeperLeague $sleeperLeague): array
    {
        $sleeperRosters = $this->sleeperApiClient->league()->listRosters($sleeperLeagueId);

        $entities = [];

        foreach ($sleeperRosters as $sleeperRoster) {
            $entity = $this->converter->toEntity($sleeperRoster);

            if (array_key_exists($entity->getOwnerId(), $sleeperUsers)) {
                $entity->setOwner($sleeperUsers[$entity->getOwnerId()]);
            }

            $entity->setLeague($sleeperLeague);

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $entities[$entity->getRosterId()] = $entity;
        }

        return $entities;
    }
}
