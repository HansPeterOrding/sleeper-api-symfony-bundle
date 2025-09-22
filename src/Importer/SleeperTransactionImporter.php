<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperTransactionConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransaction;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTransaction;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperTransactionBatch;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperRosterRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperUserRepository;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @property SleeperTransactionConverter $converter
 */
class SleeperTransactionImporter extends AbstractImporter {
    public function __construct(
        ConverterInterface                       $converter,
        EntityManagerInterface                   $entityManager,
        private readonly MessageBusInterface     $messageBus,
        private readonly SleeperUserRepository   $sleeperUserRepository,
        private readonly SleeperRosterRepository $sleeperRosterRepository,
        private readonly SleeperPlayerRepository $sleeperPlayerRepository
    )
    {
        parent::__construct($converter, $entityManager);
    }

    /**
     * @return SleeperTransaction[]
     */
    public function importTransactions(SleeperLeague $sleeperLeague, array $weeks): array
    {
        foreach ($weeks as $week) {
            $sleeperTransactions = $this->sleeperApiClient->league()->listTransactions($sleeperLeague->getLeagueId(), $week);

            foreach ($sleeperTransactions as $sleeperTransaction) {
                $message = new SyncSleeperTransaction(
                    $sleeperLeague->getLeagueId(),
                    $sleeperTransaction
                );
                $this->messageBus->dispatch($message);

                $message = null;
            }

            $sleeperTransactions = null;
        }

        return $sleeperTransactions;
    }

    /**
     * @return SleeperTransaction[]
     */
    public function importTransactionBatch(SleeperLeague $sleeperLeague, array $weeks): array
    {
        $collectedTransactions = [];

        foreach ($weeks as $week) {
            $sleeperTransactions = $this->sleeperApiClient->league()->listTransactions($sleeperLeague->getLeagueId(), $week);
            $collectedTransactions = array_merge($collectedTransactions, $sleeperTransactions);

            $message = new SyncSleeperTransactionBatch(
                $sleeperLeague->getLeagueId(),
                $sleeperTransactions
            );
            $this->messageBus->dispatch($message);
        }

        return $collectedTransactions;
    }
}
