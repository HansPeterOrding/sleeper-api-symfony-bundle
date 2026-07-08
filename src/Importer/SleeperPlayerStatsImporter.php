<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerStatsBatch;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @property ConverterInterface $converter (wird hier nicht benutzt, nur für AbstractImporter-Kompatibilität)
 */
class SleeperPlayerStatsImporter extends AbstractImporter
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
     * Importiert Player-Stats für eine Season / Week (oder Season-totals, wenn
     * $week = null) und dispatcht Batch-Messages.
     *
     * $positions wird als EIN Array an den Client übergeben -> EIN HTTP-Call an
     * Sleeper pro Aufruf dieser Methode, unabhängig davon wie viele Positionen
     * angefragt werden (Sleeper akzeptiert position[]=... mehrfach in der
     * Query-String). Die Granularität "ein Call pro Woche" bzw. "ein Call pro
     * Season-Total" wird vom Aufrufer bestimmt (siehe DispatchStatsSyncCommand),
     * nicht hier.
     *
     * @param string $season z.B. "2025"
     * @param int|null $week null = "season"-Eintrag, ansonsten Week
     * @param string[] $positions z.B. ['QB','RB','WR','TE']
     * @param int $batchSize Anzahl Player pro Batch-Message
     */
    public function importPlayerStats(
        string $season,
        ?int   $week = null,
        array  $positions = ['QB', 'RB', 'WR', 'TE', 'K', 'DEF', 'DL', 'LB', 'DB'],
        int    $batchSize = self::DEFAULT_BATCH_SIZE,
    ): void
    {
        $stats = $this->sleeperApiClient->stats()->list(
            $season,
            AbstractEndpoint::SEASON_TYPE_REGULAR,
            $week,
            $positions,
        );

        if (!$stats) {
            return;
        }

        foreach (array_chunk($stats, $batchSize) as $chunk) {
            $this->messageBus->dispatch(new SyncSleeperPlayerStatsBatch($season, $week, $chunk));
        }

        unset($stats);
        gc_collect_cycles();
    }
}
