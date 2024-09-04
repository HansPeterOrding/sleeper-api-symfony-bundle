<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperMatchupConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperLeagueRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperRosterRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

#[AsMessageHandler]
class SyncSleeperMatchupHandler
{
    public function __construct(
        private readonly SleeperLeagueRepository $sleeperLeagueRepository,
        private readonly SleeperRosterRepository $sleeperRosterRepository,
        private readonly SleeperMatchupConverter $sleeperMatchupConverter,
        private readonly SleeperPlayerRepository $sleeperPlayerRepository,
        private readonly EntityManagerInterface  $entityManager,
        private readonly LoggerInterface         $slackDebugLogger
    ) {
    }

    public function __invoke(SyncSleeperMatchup $message)
    {
        try {
            $sleeperLeagueEntity = $this->sleeperLeagueRepository->findOneBy([
                'leagueId' => $message->leagueId
            ]);
            $sleeperRosterEntity = $this->sleeperRosterRepository->findOneBy([
                'leagueId' => $message->leagueId,
                'rosterId' => $message->matchup->getRosterId()
            ]);

            $sleeperMatchupEntity = $this->sleeperMatchupConverter->toEntity($message->leagueId, $message->week, $message->matchup);
            $sleeperMatchupEntity->setLeague($sleeperLeagueEntity);
            $sleeperMatchupEntity->setRoster($sleeperRosterEntity);
            if($sleeperMatchupEntity->getPlayers()) {
                foreach ($sleeperMatchupEntity->getPlayers() as $playerId) {
                    $sleeperPlayerEntity = $this->sleeperPlayerRepository->findOneBy([
                        'playerId' => $playerId
                    ]);
                    $sleeperMatchupEntity->addSleeperPlayer($sleeperPlayerEntity);
                    if ($sleeperMatchupEntity->getStarters() && in_array($playerId, $sleeperMatchupEntity->getStarters())) {
                        $sleeperMatchupEntity->addSleeperStarterPlayer($sleeperPlayerEntity);
                    }
                }
            }

            $this->entityManager->persist($sleeperMatchupEntity);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            $this->slackDebugLogger->critical(
                'SyncSleeperMatchupHandler command error!',
                [
                    'message' => $e->getMessage(),
                    'leagueId' => $message->leagueId,
                    'week' => $message->week,
                    'matchup' => $message->matchup,
                    'previous' => $e->getPrevious()
                ]
            );
            throw new UnrecoverableMessageHandlingException($e->getMessage());
        }
    }
}
