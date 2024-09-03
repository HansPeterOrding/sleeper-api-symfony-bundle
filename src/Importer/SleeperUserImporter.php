<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperUserConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser;
use HansPeterOrding\SleeperApiSymfonyBundle\Exception\ImportException;

/**
 * @property SleeperUserConverter $converter
 */
class SleeperUserImporter extends AbstractImporter {
    public function import(string $sleeperUserId): SleeperUser
    {
        $sleeperUser = $this->sleeperApiClient->user()->get($sleeperUserId);

        if (!$sleeperUser) {
            throw new ImportException(sprintf('Draft with sleeperDraftId %s not found', $sleeperUserId));
        }

        $entity = $this->converter->toEntity($sleeperUser);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @return SleeperUser[]
     */
    public function importLeagueUsers(string $sleeperLeagueId): array
    {
        $sleeperUsers = $this->sleeperApiClient->league()->listUsers($sleeperLeagueId);

        $entities = [];

        foreach ($sleeperUsers as $sleeperUser) {
            $entity = $this->converter->toEntity($sleeperUser);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $entities[$entity->getUserId()] = $entity;
        }

        return $entities;
    }
}
