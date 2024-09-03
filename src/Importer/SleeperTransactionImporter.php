<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiClient\ApiClient\Exception\NotFoundException;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperDraftPickConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperPlayoffMatchupConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperRosterConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperTradedPickConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperTransactionConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransaction;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperMatchupRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperRosterRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperUserRepository;

/**
 * @property SleeperTransactionConverter $converter
 */
class SleeperTransactionImporter extends AbstractImporter
{
    public function __construct(
        ConverterInterface                       $converter,
        EntityManagerInterface                   $entityManager,
        private readonly SleeperUserRepository $sleeperUserRepository,
        private readonly SleeperRosterRepository $sleeperRosterRepository,
        private readonly SleeperPlayerRepository $sleeperPlayerRepository
    )
    {
        parent::__construct($converter, $entityManager);
    }

    /**
     * @return SleeperTransaction[]
     */
    public function importTransactions(SleeperLeague $sleeperLeague): array
    {
        $entities = [];

        for ($i = 1; $i <= 18; $i++) {
            $sleeperTransactions = $this->sleeperApiClient->league()->listTransactions($sleeperLeague->getLeagueId(), 1);

            foreach ($sleeperTransactions as $sleeperTransaction) {
                $entity = $this->converter->toEntity(
                    $sleeperTransaction,
                );

                $entity->setLeague($sleeperLeague);

                if($entity->getCreator()) {
                    $creatorUser = $this->sleeperUserRepository->findOneBy([
                        'userId' => $entity->getCreator()
                    ]);
                    if($creatorUser) {
                        $entity->setCreatorUser($creatorUser);
                    }
                }

                if($entity->getRosterIds()) {
                    foreach ($entity->getRosterIds() as $rosterId) {
                        $sleeperRosterEntity = $this->sleeperRosterRepository->findOneBy(
                            [
                                'leagueId' => $sleeperLeague->getLeagueId(),
                                'rosterId' => $rosterId
                            ]
                        );
                        if ($sleeperRosterEntity) {
                            $entity->addRoster($sleeperRosterEntity);
                        }
                    }
                }

                if($entity->getDrops()) {
                    foreach ($entity->getDrops() as $playerId => $rosterId) {
                        $playerEntity = $this->sleeperPlayerRepository->findOneBy([
                            'playerId' => $playerId
                        ]);
                        if ($playerEntity) {
                            $entity->addDroppedPlayer($playerEntity);
                        }
                    }
                }

                if($entity->getAdds()) {
                    foreach ($entity->getAdds() as $playerId => $rosterId) {
                        $playerEntity = $this->sleeperPlayerRepository->findOneBy([
                            'playerId' => $playerId
                        ]);
                        if ($playerEntity) {
                            $entity->addAddedPlayer($playerEntity);
                        }
                    }
                }

                if($entity->getConsenterIds()) {
                    foreach ($entity->getConsenterIds() as $rosterId) {
                        $sleeperRosterEntity = $this->sleeperRosterRepository->findOneBy([
                            'leagueId' => $sleeperLeague->getLeagueId(),
                            'rosterId' => $rosterId
                        ]);

                        if ($sleeperRosterEntity) {
                            $entity->addConsenterRoster($sleeperRosterEntity);
                        }
                    }
                }

                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $entities[] = $entity;
            }

            return $entities;
        }
    }
}
