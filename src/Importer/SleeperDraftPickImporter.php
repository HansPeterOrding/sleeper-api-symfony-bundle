<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\Exception\NotFoundException;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperDraftPickConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperRosterConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerRepository;

/**
 * @property SleeperDraftPickConverter $converter
 */
class SleeperDraftPickImporter extends AbstractImporter {
    public function __construct(
        private readonly SleeperPlayerRepository $sleeperPlayerRepository,
        ConverterInterface                       $converter,
        EntityManagerInterface                   $entityManager
    )
    {
        parent::__construct($converter, $entityManager);
    }

    /**
     * @return SleeperDraftPick[]
     */
    public function importDraftPicks(SleeperDraft $sleeperDraft): array
    {
        $sleeperDraftPicks = $this->sleeperApiClient->draft()->listPicks($sleeperDraft->getDraftId());

        $entities = [];

        foreach ($sleeperDraftPicks as $sleeperDraftPick) {
            $entity = $this->converter->toEntity($sleeperDraftPick);
            $entity->setDraft($sleeperDraft);

            $sleeperPlayer = $this->sleeperPlayerRepository->findOneBy([
                'playerId' => $entity->getPlayerId()
            ]);

            if ($sleeperPlayer) {
                $entity->setPlayer($sleeperPlayer);
            }

            $this->entityManager->persist($entity);
            $this->entityManager->flush();

            $entities[] = $entity;
        }

        return $entities;
    }
}
