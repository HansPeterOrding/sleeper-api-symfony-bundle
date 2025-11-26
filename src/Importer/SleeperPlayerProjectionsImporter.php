<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerProjectionsBatch;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @property ConverterInterface $converter (wird hier nicht benutzt, nur für AbstractImporter-Kompatibilität)
 */
class SleeperPlayerProjectionsImporter extends AbstractImporter
{
    private const DEFAULT_BATCH_SIZE = 200;

    public function __construct(
        ConverterInterface                   $converter,
        EntityManagerInterface               $entityManager,
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct($converter, $entityManager);
    }

    /**
     * Importiert Player-Projections für eine Season/Week und dispatcht Batch-Messages.
     *
     * @param string        $season
     * @param int           $week
     * @param string[]      $positions
     * @param int           $batchSize
     */
    public function importPlayerProjections(
        string        $season,
        ?int   $week = null,
        array         $positions = ['QB', 'RB', 'WR', 'TE', 'K', 'DEF', 'DL', 'LB', 'DB'],
        int           $batchSize = self::DEFAULT_BATCH_SIZE,
    ): void {
        $projections = $this->sleeperApiClient->projections()->list(
            $season,
            AbstractEndpoint::SEASON_TYPE_REGULAR,
            $week,
            $positions,
        );

        if (!$projections) {
            return;
        }

        // 2) in Batches splitten und Messages dispatchen
        $chunks       = array_chunk($projections, $batchSize);
        $isFirstBatch = true;

        foreach ($chunks as $chunk) {
            $message = new SyncSleeperPlayerProjectionsBatch(
                $season,
                $week,
                $chunk,
            );

            $this->messageBus->dispatch($message);
            $isFirstBatch = false;
        }
    }
}
