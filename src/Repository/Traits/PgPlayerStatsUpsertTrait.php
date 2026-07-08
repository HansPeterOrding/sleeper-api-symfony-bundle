<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits;

use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjections;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperStats;

/**
 * Shared row-building and bulk-upsert plumbing for
 * SleeperPlayerStatsRepository::pgBulkUpsertStats() and
 * SleeperPlayerProjectionsRepository::pgBulkUpsertProjections().
 *
 * Both target tables (sasb_sleeper_player_stats / sasb_sleeper_player_projections)
 * share an identical column shape: the same fixed base columns plus one column
 * per property of the shared `SleeperStats` embeddable (241 columns, reflected
 * once and cached for the process lifetime). Sharing this trait keeps that SQL
 * single-sourced instead of duplicated across two repositories.
 *
 * Requires PostgresPlatformAssertionTrait (composed below) for db()/assertPostgres().
 */
trait PgPlayerStatsUpsertTrait
{
    use PostgresPlatformAssertionTrait;

    /**
     * Sentinel value for the `week` column when a row represents a season-total
     * entry (no specific week).
     *
     * Why not NULL: the unique index backing our ON CONFLICT target is a plain
     * btree unique index on (season, week, player_id) — NOT a partial index with
     * a NULLS NOT DISTINCT clause. In standard Postgres uniqueness semantics,
     * NULL is never equal to NULL, so a row with week = NULL can never match an
     * existing row via ON CONFLICT: every "redelivery" of a season-total row
     * would silently INSERT a fresh duplicate instead of updating the existing
     * one. Using 0 (never a real NFL week number) keeps season-total rows inside
     * the conflict target and makes redelivery a clean, idempotent no-op.
     */
    public const int SEASON_TOTAL_WEEK = 0;

    /** @var array<string,string>|null [propertyName => columnName], cached per process */
    private static ?array $statsFieldMapCache = null;

    /**
     * Maps a client DTO onto a flat DB row.
     *
     * $season and $week are the CALLER-PROVIDED (message-level) values, and are
     * authoritative over whatever the DTO itself reports — the same lesson
     * already applied to traded picks (caller-provided draftId over DTO field).
     * This sidesteps any question of whether a given Sleeper response item
     * echoes "week" per-entry, and guarantees every row in a batch lands under
     * the same (season, week) partition the batch was actually fetched for.
     *
     * @return array<string,mixed>|null null if the external player_id has no
     *         matching row in sasb_sleeper_player yet (row is dropped, not
     *         inserted — a later re-sync of this same week will pick it up
     *         once the player exists locally).
     */
    protected function buildRow(
        SleeperPlayerStats|SleeperPlayerProjections $dto,
        array                                       $playerIdMap,
        string                                      $season,
        ?int                                        $week,
    ): ?array
    {
        $playerId = (string)$dto->getPlayerId();
        $internalPlayerId = $playerIdMap[$playerId] ?? null;

        if ($internalPlayerId === null) {
            return null;
        }

        $statsDto = $dto->getStats();
        $date = $dto->getDate();

        $row = [
            'sleeper_player_id' => $internalPlayerId,
            'week' => $week ?? self::SEASON_TOTAL_WEEK,
            'team' => $dto->getTeam(),
            'sport' => $dto->getSport(),
            'season_type' => $dto->getSeasonType(),
            'season' => $season,
            'player_id' => $playerId,
            'opponent' => $dto->getOpponent(),
            'game_id' => $dto->getGameId(),
            'date' => $date instanceof \DateTimeInterface ? $date->format('Y-m-d H:i:s') : null,
            'company' => $dto->getCompany(),
            'category' => $dto->getCategory(),
        ];

        foreach ($this->getStatsFieldMap() as $propertyName => $columnName) {
            $getter = 'get' . ucfirst($propertyName);
            $row[$columnName] = ($statsDto !== null && method_exists($statsDto, $getter))
                ? $statsDto->$getter()
                : null;
        }

        return $row;
    }

    /**
     * @return array<string,string> [propertyName => columnName], e.g. "gmsActive" => "stats_gms_active"
     */
    protected function getStatsFieldMap(): array
    {
        if (self::$statsFieldMapCache !== null) {
            return self::$statsFieldMapCache;
        }

        $map = [];
        foreach ((new \ReflectionClass(SleeperStats::class))->getProperties() as $property) {
            $name = $property->getName();
            $map[$name] = 'stats_' . $this->toSnakeCase($name);
        }

        return self::$statsFieldMapCache = $map;
    }

    private function toSnakeCase(string $input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    /**
     * Multi-VALUES upsert, chunked (200 rows/statement) and wrapped in a single
     * transaction so a partial failure across chunks can't leave the batch
     * half-applied. Fails loudly if rows don't share an identical column shape
     * rather than silently emitting misaligned SQL.
     *
     * @param string[] $conflictColumns
     */
    protected function pgUpsertRows(string $table, array $rows, array $conflictColumns): void
    {
        if ($rows === []) {
            return;
        }
        $this->assertPostgres();

        $columns = array_keys($rows[0]);
        foreach ($rows as $i => $row) {
            if (array_keys($row) !== $columns) {
                throw new \LogicException(sprintf(
                    '%s::pgUpsertRows(): row %d has a different column shape than row 0 (table %s); refusing to build misaligned SQL.',
                    static::class,
                    $i,
                    $table,
                ));
            }
        }

        $db = $this->db();
        $db->beginTransaction();
        try {
            foreach (array_chunk($rows, 200) as $chunk) {
                $this->pgUpsertRowsChunk($table, $columns, $chunk, $conflictColumns);
            }
            $db->commit();
        } catch (\Throwable $e) {
            if ($db->isTransactionActive()) {
                $db->rollBack();
            }
            throw $e;
        }
    }

    /**
     * @param string[] $columns
     * @param array<int,array<string,mixed>> $chunk
     * @param string[] $conflictColumns
     */
    private function pgUpsertRowsChunk(string $table, array $columns, array $chunk, array $conflictColumns): void
    {
        $placeholdersPerRow = '(' . implode(', ', array_fill(0, count($columns), '?')) . ')';
        $valuesSql = implode(', ', array_fill(0, count($chunk), $placeholdersPerRow));

        $updateAssignments = [];
        foreach ($columns as $col) {
            if (\in_array($col, $conflictColumns, true)) {
                continue;
            }
            $updateAssignments[] = sprintf('%s = EXCLUDED.%s', $col, $col);
        }

        $sql = sprintf(
            'INSERT INTO public.%s (%s) VALUES %s ON CONFLICT (%s) DO UPDATE SET %s',
            $table,
            implode(', ', $columns),
            $valuesSql,
            implode(', ', $conflictColumns),
            implode(', ', $updateAssignments),
        );

        $params = [];
        foreach ($chunk as $row) {
            foreach ($columns as $col) {
                $params[] = $row[$col] ?? null;
            }
        }

        $this->db()->executeStatement($sql, $params);
    }
}
