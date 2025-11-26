<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\Connection;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerStatsBatch;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperPlayerStatsBatchHandler
{
    public function __construct(
        private readonly Connection $db,
    ) {
    }

    public function __invoke(SyncSleeperPlayerStatsBatch $message): void
    {
        if (empty($message->stats)) {
            return;
        }

        $this->db->beginTransaction();
        try {
            $externalPids = array_map(
                static fn(SleeperPlayerStats $dto) => (string)$dto->getPlayerId(),
                $message->stats
            );
            $playerIdMap = $this->fetchPlayerIdMap($externalPids);

            $this->bulkUpsertStats($message, $playerIdMap);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param string[] $externalPids
     * @return array<string,int> [external player_id => internal id]
     */
    private function fetchPlayerIdMap(array $externalPids): array
    {
        $externalPids = array_values(array_filter(array_map(static fn($v) => trim((string)$v), $externalPids)));
        if ($externalPids === []) {
            return [];
        }

        $externalPids = array_values(array_unique($externalPids));
        $map          = [];
        $chunkSize    = 1000;

        foreach (array_chunk($externalPids, $chunkSize) as $chunk) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT player_id, id
                   FROM public.sasb_sleeper_player
                  WHERE player_id IN (?)',
                [$chunk],
                [Connection::PARAM_STR_ARRAY]
            );

            foreach ($rows as $row) {
                $map[(string)$row['player_id']] = (int)$row['id'];
            }
        }

        return $map;
    }

    /**
     * @param array<string,int> $playerIdMap
     */
    private function bulkUpsertStats(SyncSleeperPlayerStatsBatch $message, array $playerIdMap): void
    {
        // 🔧 Spaltenliste – bitte komplett machen (alle stats_ Felder, die du importierst)
        $columns = [
            'sleeper_player_id',
            'week',
            'team',
            'sport',
            'season_type',
            'season',
            'player_id',
            'opponent',
            'game_id',
            'date',
            'company',
            'category',

            'stats_gp',
            'stats_gms_active',
            'stats_gs',
            'stats_pts_std',
            'stats_pts_half_ppr',
            'stats_pts_ppr',
            'stats_rank_std',
            'stats_rank_ppr',
            'stats_pos_rank_std',
            'stats_pos_rank_ppr',
            // ... alle weiteren stats_-Spalten ...
        ];

        $rows   = [];
        $params = [];

        foreach ($message->stats as $dto) {
            if (!$dto instanceof SleeperPlayerStats) {
                continue;
            }

            $playerId       = (string)$dto->getPlayerId();
            $internalPlayer = $playerIdMap[$playerId] ?? null;

            // Wenn Spieler unbekannt ist, kannst du entweder:
            // - ihn ignorieren, oder
            // - später eine Warning loggen. Hier: ignorieren.
            if ($internalPlayer === null) {
                continue;
            }

            $statsDto = $dto->getStats();
            $date     = $dto->getDate();

            $rawWeek = $dto->getWeek();
            $week    = $rawWeek ?? 0;

            $row = [
                'sleeper_player_id' => $internalPlayer,
                'week'              => $week,
                'team'              => $dto->getTeam(),
                'sport'             => $dto->getSport(),
                'season_type'       => $dto->getSeasonType(),
                'season'            => $dto->getSeason(),
                'player_id'         => $playerId,
                'opponent'          => $dto->getOpponent(),
                'game_id'           => $dto->getGameId(),
                'date'              => $date instanceof \DateTimeInterface ? $date->format('Y-m-d H:i:s') : null,
                'company'           => $dto->getCompany(),
                'category'          => $dto->getCategory(),

                'stats_gp'              => $statsDto?->getGp(),
                'stats_gms_active'      => $statsDto?->getGmsActive(),
                'stats_gs'              => $statsDto?->getGs(),
                'stats_pts_std'         => $statsDto?->getPtsStd(),
                'stats_pts_half_ppr'    => $statsDto?->getPtsHalfPpr(),
                'stats_pts_ppr'         => $statsDto?->getPtsPpr(),
                'stats_rank_std'        => $statsDto?->getRankStd(),
                'stats_rank_ppr'        => $statsDto?->getRankPpr(),
                'stats_pos_rank_std'    => $statsDto?->getPosRankStd(),
                'stats_pos_rank_ppr'    => $statsDto?->getPosRankPpr(),
                // ... alle weiteren Getter entsprechend zuordnen ...
            ];

            $rows[] = $row;
        }

        if ($rows === []) {
            return;
        }

        // Platzhalter bauen
        $placeholdersPerRow = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $valuesSqlParts     = [];

        foreach ($rows as $row) {
            $valuesSqlParts[] = $placeholdersPerRow;
            foreach ($columns as $col) {
                $params[] = $row[$col] ?? null;
            }
        }

        // UPDATE-Teil: alle Spalten außer den Konfliktspalten aktualisieren
        $conflictColumns = ['sleeper_player_id', 'season', 'week']; // entspricht deinem Unique-Index
        $updateAssignments = [];
        foreach ($columns as $col) {
            if (\in_array($col, $conflictColumns, true)) {
                continue;
            }
            $updateAssignments[] = sprintf('%s = EXCLUDED.%s', $col, $col);
        }

        $sql = sprintf(
            'INSERT INTO public.sasb_sleeper_player_stats (%s) VALUES %s
     ON CONFLICT (season, week, player_id)
     DO UPDATE SET %s',
            implode(', ', $columns),
            implode(', ', $valuesSqlParts),
            implode(', ', $updateAssignments),
        );

        $this->db->executeStatement($sql, $params);
    }
}
