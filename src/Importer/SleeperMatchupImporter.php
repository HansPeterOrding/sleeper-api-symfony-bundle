<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use App\Message\SleeperSync\SyncSleeperMatchup;
use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\Exception\NotFoundException;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperMatchupConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperRosterConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerRepository;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @property SleeperMatchupConverter $converter
 */
class SleeperMatchupImporter extends AbstractImporter
{
    public function __construct(
        ConverterInterface                       $converter,
        EntityManagerInterface                   $entityManager, private readonly MessageBusInterface $messageBus
    ) {
        parent::__construct($converter, $entityManager);
    }

    /**
     * @return SleeperDraftPick[]
     */
    public function importMatchups(SleeperLeague $sleeperLeague, array $sleeperRosters): void
    {
        for($week = 1; $week <= 18; $week++) {
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
}
