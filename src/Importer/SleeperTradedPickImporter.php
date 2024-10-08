<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperTradedPickConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;

/**
 * @property SleeperTradedPickConverter $converter
 */
class SleeperTradedPickImporter extends AbstractImporter {
    public function __construct(
        ConverterInterface     $converter,
        EntityManagerInterface $entityManager
    )
    {
        parent::__construct($converter, $entityManager);
    }

    /**
     * @return SleeperDraftPick[]
     */
    public function importTradedPicks(SleeperLeague $sleeperLeague, ?SleeperDraft $sleeperDraft): array
    {
        $sleeperTradedPicks = $this->sleeperApiClient->draft()->listTradedPicks($sleeperLeague->getDraftId());

        $entities = [];

        foreach ($sleeperTradedPicks as $sleeperTradedPick) {
            $entity = $this->converter->toEntity(
                $sleeperLeague->getLeagueId(),
                $sleeperLeague->getDraftId(),
                $sleeperTradedPick
            );

            $entity->setLeague($sleeperLeague);
            $entity->setDraft($sleeperDraft);

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $entities[] = $entity;
        }

        return $entities;
    }
}
