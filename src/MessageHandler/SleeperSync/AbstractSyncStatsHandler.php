<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\Connection;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjections;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperStats;
use Symfony\Component\Console\Command\Command;

abstract class AbstractSyncStatsHandler {
    /** @var array<string,string>|null  [propertyName => columnName] */
    protected ?array $statsFieldMap = null;

    public function __construct(
        protected Connection $db,
    ) {
    }

    protected function buildRow(SleeperPlayerStats|SleeperPlayerProjections $dto, array $playerIdMap): ?array
    {
        $playerId       = (string)$dto->getPlayerId();
        $internalPlayer = $playerIdMap[$playerId] ?? null;

        if ($internalPlayer === null) {
            return null;
        }

        $statsDto = $dto->getStats();
        $date     = $dto->getDate();

        $rawWeek = $dto->getWeek();
        $week    = $rawWeek ?? 0;

        $row = [
            'sleeper_player_id' => $internalPlayer,
            'week'              => $week, // theoretisch auch null möglich
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
        ];

        // Alle Stats-Felder dynamisch aus $statsDto befüllen
        $statsFieldMap = $this->getStatsFieldMap();

        foreach ($statsFieldMap as $propertyName => $columnName) {
            // Getter-Name z.B. "gmsActive" -> "getGmsActive"
            $getter = 'get' . ucfirst($propertyName);

            if ($statsDto === null || !method_exists($statsDto, $getter)) {
                // Stats-Objekt fehlt oder der DTO kennt das Feld (noch) nicht
                $row[$columnName] = null;
                continue;
            }

            $row[$columnName] = $statsDto->$getter();
        }

        return $row;
    }

    protected function upsert(array $rows, string $type = 'stats'): void
    {
        $columns = array_keys($rows[0]);

        $placeholdersPerRow = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $valuesSqlParts     = [];

        foreach ($rows as $row) {
            $valuesSqlParts[] = $placeholdersPerRow;
            foreach ($columns as $col) {
                $params[] = $row[$col] ?? null;
            }
        }

        $conflictColumns   = ['player_id', 'season', 'week'];
        $updateAssignments = [];
        foreach ($columns as $col) {
            if (\in_array($col, $conflictColumns, true)) {
                continue;
            }
            $updateAssignments[] = sprintf('%s = EXCLUDED.%s', $col, $col);
        }

        $sql = sprintf(
            'INSERT INTO public.%s (%s) VALUES %s
     ON CONFLICT (season, week, player_id)
     DO UPDATE SET %s',
            $type === 'stats'?'sasb_sleeper_player_stats':'sasb_sleeper_player_projections',
            implode(', ', $columns),
            implode(', ', $valuesSqlParts),
            implode(', ', $updateAssignments),
        );

        $this->db->executeStatement($sql, $params);
    }

    /**
     * Erzeugt ein Mapping aus Property-Namen von SleeperStats
     * auf die Spaltennamen in der Projections-Tabelle.
     *
     * Beispiel:
     *  - "gp"        => "stats_gp"
     *  - "gmsActive" => "stats_gms_active"
     */
    protected function getStatsFieldMap(): array
    {
        if ($this->statsFieldMap !== null) {
            return $this->statsFieldMap;
        }

        $ref = new \ReflectionClass(SleeperStats::class);
        $map = [];

        foreach ($ref->getProperties() as $property) {
            $name = $property->getName();               // z.B. "gmsActive"
            $column = 'stats_' . $this->toSnakeCase($name); // "stats_gms_active"
            $map[$name] = $column;
        }

        $this->statsFieldMap = $map;

        return $this->statsFieldMap;
    }

    protected function toSnakeCase(string $input): string
    {
        // "gmsActive" -> "gms_active", "ptsPpr" -> "pts_ppr"
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }



    /**
     * @param string[] $externalPids
     * @return array<string,int>
     */
    protected function fetchPlayerIdMap(array $externalPids): array
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
}
