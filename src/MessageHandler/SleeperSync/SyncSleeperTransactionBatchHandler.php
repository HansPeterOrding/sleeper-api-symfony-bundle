<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperTransactionConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTransactionBatch;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperLeagueRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperRosterRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperUserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
class SyncSleeperTransactionBatchHandler {
    public function __construct(
        private readonly SleeperLeagueRepository     $sleeperLeagueRepository,
        private readonly SleeperUserRepository       $sleeperUserRepository,
        private readonly SleeperRosterRepository     $sleeperRosterRepository,
        private readonly SleeperTransactionConverter $sleeperTransactionConverter,
        private readonly SleeperPlayerRepository     $sleeperPlayerRepository,
        private readonly EntityManagerInterface      $entityManager,
        private readonly LoggerInterface             $slackDebugLogger,
    )
    {
    }

    public function __invoke(SyncSleeperTransactionBatch $message)
    {
        try {
            $sleeperLeagueEntity = $this->sleeperLeagueRepository->findOneBy([
                'leagueId' => $message->leagueId
            ]);

            foreach ($message->transactions as $transaction) {
                $entity = $this->sleeperTransactionConverter->toEntity(
                    $transaction,
                );

                $timestampSec = $entity->getStatusUpdated()/1000;
                if($entity->getUpdatedAt() && $entity->getUpdatedAt() > DateTime::createFromFormat('U.u', (string)$timestampSec)) {
                    continue;
                }

                $entity->setLeague($sleeperLeagueEntity);

                if ($entity->getCreator()) {
                    $creatorUser = $this->sleeperUserRepository->findOneBy([
                        'userId' => $entity->getCreator()
                    ]);
                    if ($creatorUser) {
                        $entity->setCreatorUser($creatorUser);
                    }
                }

                if ($entity->getRosterIds()) {
                    foreach ($entity->getRosterIds() as $rosterId) {
                        $sleeperRosterEntity = $this->sleeperRosterRepository->findOneBy(
                            [
                                'leagueId' => $sleeperLeagueEntity->getLeagueId(),
                                'rosterId' => $rosterId
                            ]
                        );
                        if ($sleeperRosterEntity) {
                            $entity->addRoster($sleeperRosterEntity);
                        }
                    }
                }

                if ($entity->getDrops()) {
                    foreach ($entity->getDrops() as $playerId => $rosterId) {
                        $playerEntity = $this->sleeperPlayerRepository->findOneBy([
                            'playerId' => $playerId
                        ]);
                        if ($playerEntity) {
                            $entity->addDroppedPlayer($playerEntity);
                        }
                    }
                }

                if ($entity->getAdds()) {
                    foreach ($entity->getAdds() as $playerId => $rosterId) {
                        $playerEntity = $this->sleeperPlayerRepository->findOneBy([
                            'playerId' => $playerId
                        ]);
                        if ($playerEntity) {
                            $entity->addAddedPlayer($playerEntity);
                        }
                    }
                }

                if ($entity->getConsenterIds()) {
                    foreach ($entity->getConsenterIds() as $rosterId) {
                        $sleeperRosterEntity = $this->sleeperRosterRepository->findOneBy([
                            'leagueId' => $sleeperLeagueEntity->getLeagueId(),
                            'rosterId' => $rosterId
                        ]);

                        if ($sleeperRosterEntity) {
                            $entity->addConsenterRoster($sleeperRosterEntity);
                        }
                    }
                }

                $this->entityManager->persist($entity);
            }

            $this->entityManager->flush();

        } catch (\Throwable $e) {
            $this->slackDebugLogger->critical(
                'SyncSleeperTransactionBatchHandler command error!',
                [
                    'message' => $e->getMessage(),
                    'leagueId' => $message->leagueId,
                    'previous' => $e->getPrevious()
                ]
            );
            throw new UnrecoverableMessageHandlingException($e->getMessage());
        }

    }
}
