<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperMatchupConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperMatchupBatch;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @property SleeperMatchupConverter $converter
 */
class SleeperMatchupImporter extends AbstractImporter
{
    public function __construct(
        ConverterInterface                   $converter,
        EntityManagerInterface               $entityManager,
        private readonly MessageBusInterface $messageBus
    )
    {
        parent::__construct($converter, $entityManager);
    }

    /**
     * @return SleeperDraftPick[]
     */
    public function importMatchups(SleeperLeague $sleeperLeague, array $sleeperRosters, array $weeks): void
    {
        foreach ($weeks as $week) {
            $sleeperMatchups = $this->sleeperApiClient->league()->listMatchups($sleeperLeague->getLeagueId(), $week);

            foreach ($sleeperMatchups as $sleeperMatchup) {
                $message = new SyncSleeperMatchup(
                    $sleeperLeague->getLeagueId(),
                    $week,
                    $sleeperMatchup
                );
                $this->messageBus->dispatch($message);
            }
        }
    }

    public function importBulkMatchups(SleeperLeague $sleeperLeague, array $sleeperRosters, array $weeks): void
    {
        $sleeperMatchups = [];

        foreach ($weeks as $week) {
            $sleeperMatchups[$week] = $this->sleeperApiClient->league()->listMatchups($sleeperLeague->getLeagueId(), $week);
        }

        $bulkMessage = new SyncSleeperMatchupBatch(
            $sleeperLeague->getLeagueId(),
            $sleeperMatchups
        );

        $this->messageBus->dispatch($bulkMessage);
    }
}
