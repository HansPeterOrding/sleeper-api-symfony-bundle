<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerProjectionsBatch;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @property ConverterInterface $converter (wird hier nicht benutzt, nur für AbstractImporter-Kompatibilität)
 */
class SleeperPlayerProjectionsImporter extends AbstractImporter
{
    private const int DEFAULT_BATCH_SIZE = 200;

    public function __construct(
        ConverterInterface                   $converter,
        EntityManagerInterface               $entityManager,
        private readonly MessageBusInterface $messageBus,
    )
    {
        parent::__construct($converter, $entityManager);
    }

    /**
     * Importiert Player-Projections für eine Season/Week und dispatcht
     * Batch-Messages.
     *
     * $positions wird als EIN Array an den Client übergeben -> EIN HTTP-Call an
     * Sleeper pro Aufruf dieser Methode, unabhängig davon wie viele Positionen
     * angefragt werden (Sleeper akzeptiert position[]=... mehrfach in der
     * Query-String). Die Granularität "ein Call pro Woche" wird vom Aufrufer
     * bestimmt (siehe DispatchStatsSyncCommand), nicht hier.
     *
     * @param string $season
     * @param int|null $week
     * @param string[] $positions
     * @param int $batchSize Anzahl Player pro Batch-Message
     */
    public function importPlayerProjections(
        string $season,
        ?int   $week = null,
        array  $positions = ['QB', 'RB', 'WR', 'TE', 'K', 'DEF', 'DL', 'LB', 'DB'],
        int    $batchSize = self::DEFAULT_BATCH_SIZE,
    ): void
    {
        $projections = $this->sleeperApiClient->projections()->list(
            $season,
            AbstractEndpoint::SEASON_TYPE_REGULAR,
            $week,
            $positions,
        );

        if (!$projections) {
            return;
        }

        foreach (array_chunk($projections, $batchSize) as $chunk) {
            $this->messageBus->dispatch(new SyncSleeperPlayerProjectionsBatch($season, $week, $chunk));
        }

        unset($projections);
        gc_collect_cycles();
    }
}
