<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperMatchupBatch;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperMatchupBatchHandler
{
    public function __construct(private readonly Connection $db)
    {
    }

    public function __invoke(SyncSleeperMatchupBatch $message): void
    {
        $this->db->beginTransaction();
        try {
            // 1) IDs mappen (League + Roster)
            $internalLeagueId = $this->fetchInternalLeagueId($message->leagueId);
            $rosterIdMap = $this->fetchRosterMap($message->leagueId); // [roster_id => internal_roster_id]

            // 2) BULK UPSERT in sasb_sleeper_matchup (Chunked)
            $rows = [];
            foreach ($message->matchups as $week => $weeklyMatchups) {
                foreach ($weeklyMatchups as $matchup) {
                    // erwarte Felder: roster_id (int), points (float), custom_points (float|null), matchup_id (int|null),
                    // starters (array<int,string player_uid>), players (array<int,string player_uid>),
                    // starters_points, players_points (assoc[player_uid => float]) – alles Raw aus Sleeper
                    $rosterId = $matchup->getRosterId();
                    if (!$rosterId || !isset($rosterIdMap[$rosterId])) {
                        // unbekannter Roster → überspringen (oder vorher sicherstellen, dass Roster-Importer läuft)
                        continue;
                    }

                    $rows[] = [
                        'internal_league_id' => $internalLeagueId,
                        'internal_roster_id' => $rosterIdMap[$rosterId],
                        'league_id' => $message->leagueId,
                        'week' => (int)$week,
                        'starters_points' => $this->toJsonText($matchup->getStartersPoints()),
                        'players_points' => $this->toJsonText($matchup->getPlayersPoints()),
                        'starters' => $this->toJsonText($matchup->getStarters()),
                        'roster_id' => $rosterId,
                        'players' => $this->toJsonText($matchup->getPlayers()),
                        'matchup_id' => $matchup->getMatchupId(),
                        'points' => $matchup->getPoints(),
                        'custom_points' => $matchup->getCustomPoints(),
                    ];
                }


            }

            // in gut verdaulichen Stücken schreiben
            foreach (array_chunk($rows, 200) as $chunk) {
                $this->bulkUpsertMatchups($chunk);
            }

            // 3) Primärschlüssel der soeben betroffenen Matchups holen (pro Woche)
            $weekToRosterIds = [];
            foreach ($rows as $r) {
                $w = (int)$r['week'];
                $weekToRosterIds[$w][] = (int)$r['roster_id'];
            }
            // Deduplizieren
            foreach ($weekToRosterIds as $w => $list) {
                $weekToRosterIds[$w] = array_values(array_unique($list));
            }

            if ($weekToRosterIds === []) {
                $this->db->commit();
                return;
            }

            // Map: [week => [roster_id => matchup_db_id]]
            $matchupPkMapByWeek = $this->fetchMatchupPkMapByWeek($message->leagueId, $weekToRosterIds);

            // 4) Player-IDs einsammeln und auf interne IDs mappen
            [$allExtPids, $startersByMid, $playersByMid] = $this->collectPlayersForJunctionsByWeek(
                $message->matchups,
                $matchupPkMapByWeek
            );

            $playerIdMap = $this->fetchPlayerIdMap($allExtPids); // [ext => internal player.id]

// 5) Junction-Tables neu befüllen (delete + bulk insert)
            $matchupDbIds = array_keys($startersByMid + $playersByMid); // alle betroffenen mids
            if ($matchupDbIds) {
                $this->wipeJunctions($matchupDbIds);

                // starters
                $starterRows = [];
                foreach ($startersByMid as $mid => $extPids) {
                    foreach ($extPids as $pid) {
                        if (isset($playerIdMap[$pid])) {
                            $starterRows[] = ['matchup_id' => (int)$mid, 'player_id' => $playerIdMap[$pid]];
                        }
                    }
                }
                foreach (array_chunk($starterRows, 1000) as $chunk) {
                    $this->bulkInsertJunction('public.sasb_matchup_starters', $chunk);
                }

                // players
                $playerRows = [];
                foreach ($playersByMid as $mid => $extPids) {
                    foreach ($extPids as $pid) {
                        if (isset($playerIdMap[$pid])) {
                            $playerRows[] = ['matchup_id' => (int)$mid, 'player_id' => $playerIdMap[$pid]];
                        }
                    }
                }
                foreach (array_chunk($playerRows, 1000) as $chunk) {
                    $this->bulkInsertJunction('public.sasb_matchup_players', $chunk);
                }
            }

            $this->db->commit();

        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function fetchInternalLeagueId(string $leagueId): int
    {
        $id = $this->db->fetchOne(
            'SELECT id FROM public.sasb_sleeper_league WHERE league_id = ?',
            [$leagueId]
        );
        if (!$id) {
            throw new \RuntimeException("League $leagueId not found (run league importer first).");
        }
        return (int)$id;
    }

    /** @return array<int,int> [roster_id => internal_roster_id] */
    private function fetchRosterMap(string $leagueId): array
    {
        $rows = $this->db->fetchAllAssociative(
            'SELECT roster_id, id FROM public.sasb_sleeper_roster WHERE league_id = ?',
            [$leagueId]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['roster_id']] = (int)$r['id'];
        }
        return $map;
    }

    /** @param array<int,array<string,mixed>> $chunk */
    private function bulkUpsertMatchups(array $chunk): void
    {
        if ($chunk === []) return;

        // Platzhalter: JSON-Felder mit ::json casten
        $tuple = '(' . implode(',', [
                '?',          // internal_league_id
                '?',          // internal_roster_id
                '?',          // league_id
                '?',          // week
                '?::json',    // starters_points
                '?::json',    // players_points
                '?::json',    // starters
                '?',          // roster_id
                '?::json',    // players
                '?',          // matchup_id
                '?',          // points
                '?',          // custom_points
            ]) . ')';

        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_matchup
  (internal_league_id, internal_roster_id, league_id, week,
   starters_points, players_points, starters, roster_id,
   players, matchup_id, points, custom_points)
VALUES $valuesSql
SQL;

        $params = [];
        foreach ($chunk as $r) {
            $params[] = $r['internal_league_id'];
            $params[] = $r['internal_roster_id'];
            $params[] = $r['league_id'];
            $params[] = $r['week'];
            $params[] = $r['starters_points']; // JSON-Text (oder null)
            $params[] = $r['players_points'];  // JSON-Text (oder null)
            $params[] = $r['starters'];        // JSON-Text (oder null)
            $params[] = $r['roster_id'];
            $params[] = $r['players'];         // JSON-Text (oder null)
            $params[] = $r['matchup_id'];
            $params[] = $r['points'];
            $params[] = $r['custom_points'];
        }

        $this->db->executeStatement($sql, $params);
    }


    /** @param string[] $externalPids @return array<string,int> [external player_id => internal id] */
    private function fetchPlayerIdMap(array $externalPids): array
    {
        // Normalisieren: Strings, getrimmt, ohne leere
        $externalPids = array_values(array_filter(array_map(static fn($v) => trim((string)$v), $externalPids)));
        if ($externalPids === []) {
            return [];
        }

        // Deduplizieren
        $externalPids = array_values(array_unique($externalPids));

        $map = [];
        // Große IN-Listen besser in Chunks abfragen (z. B. 1000er)
        $chunkSize = 1000;

        foreach (array_chunk($externalPids, $chunkSize) as $chunk) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT player_id, id
               FROM public.sasb_sleeper_player
              WHERE player_id IN (?)',
                [$chunk],
                [ArrayParameterType::STRING] // oder: [ArrayParameterType::STRING] bei DBAL 3
            );

            foreach ($rows as $r) {
                $map[(string)$r['player_id']] = (int)$r['id'];
            }
        }

        return $map;
    }


    /** Delete alle Junction-Einträge für die betroffenen Matchups (billig & sicher). */
    private function wipeJunctions(array $matchupDbIds): void
    {
        // normalisieren & deduplizieren
        $ids = array_values(array_unique(array_map('intval', $matchupDbIds)));
        if ($ids === []) {
            return;
        }

        // große IN-Listen in Chunks aufteilen
        $chunkSize = 1000;

        foreach (array_chunk($ids, $chunkSize) as $chunk) {
            // starters
            $this->db->executeStatement(
                'DELETE FROM public.sasb_matchup_starters WHERE matchup_id IN (?)',
                [$chunk],
                [ArrayParameterType::INTEGER] // bei DBAL 3 alternativ: [ArrayParameterType::INTEGER]
            );

            // players
            $this->db->executeStatement(
                'DELETE FROM public.sasb_matchup_players WHERE matchup_id IN (?)',
                [$chunk],
                [ArrayParameterType::INTEGER] // bzw. ArrayParameterType::INTEGER
            );
        }
    }


    /** @param array<int,array{matchup_id:int,player_id:int}> $rows */
    private function bulkInsertJunction(string $table, array $rows): void
    {
        if ($rows === []) return;

        $values = implode(',', array_fill(0, count($rows), '(?,?)'));
        $sql = "INSERT INTO {$table} (matchup_id, player_id) VALUES {$values} ON CONFLICT DO NOTHING";

        $params = [];
        foreach ($rows as $r) {
            $params[] = $r['matchup_id'];
            $params[] = $r['player_id'];
        }
        $this->db->executeStatement($sql, $params);
    }

    /**
     * Hole PK-IDs der Matchups pro Woche.
     * @param array<int,int[]> $weekToRosterIds z.B. [1 => [3,7,9], 2 => [5,6]]
     * @return array<int,array<int,int>>         [week => [roster_id => matchup_id]]
     */
    private function fetchMatchupPkMapByWeek(string $leagueId, array $weekToRosterIds): array
    {
        $out = [];
        foreach ($weekToRosterIds as $week => $rosterIds) {
            $week = (int)$week;
            $rosterIds = array_values(array_unique(array_map('intval', $rosterIds)));
            if (!$rosterIds) {
                $out[$week] = [];
                continue;
            }

            $rows = $this->db->fetchAllAssociative(
                'SELECT id, roster_id
               FROM public.sasb_sleeper_matchup
              WHERE league_id = ?
                AND week = ?
                AND roster_id IN (?)',
                [$leagueId, $week, $rosterIds],
                [ParameterType::STRING, ParameterType::INTEGER, ArrayParameterType::INTEGER]
            );

            $map = [];
            foreach ($rows as $r) {
                $map[(int)$r['roster_id']] = (int)$r['id'];
            }
            $out[$week] = $map;
        }
        return $out;
    }


    /**
     * Baut aus message->matchups [week => SleeperMatchup[]] die Zuordnung für Junctions.
     * @param array<int,array<int,SleeperMatchup>> $matchupsByWeek
     * @param array<int,array<int,int>> $pkMapByWeek [week => [roster_id => matchup_id]]
     * @return array{0: string[], 1: array<int,string[]>, 2: array<int,string[]>}
     *         [alle_ext_pids, startersByMid[mid]=>[extPid..], playersByMid[mid]=>[extPid..]]
     */
    private function collectPlayersForJunctionsByWeek(array $matchupsByWeek, array $pkMapByWeek): array
    {
        $all = [];
        $startersByMid = [];
        $playersByMid = [];

        foreach ($matchupsByWeek as $week => $weekly) {
            $w = (int)$week;
            $rosterToMid = $pkMapByWeek[$w] ?? [];
            foreach ($weekly as $matchup) {
                $rid = (int)$matchup->getRosterId();
                if (!$rid || !isset($rosterToMid[$rid])) {
                    continue;
                }
                $mid = $rosterToMid[$rid];

                $st = array_values(array_filter(array_map('strval', $matchup->getStarters() ?? [])));
                $pl = array_values(array_filter(array_map('strval', $matchup->getPlayers() ?? [])));

                if ($st) {
                    $startersByMid[$mid] = $st;
                    foreach ($st as $pid) {
                        $all[$pid] = true;
                    }
                }
                if ($pl) {
                    $playersByMid[$mid] = $pl;
                    foreach ($pl as $pid) {
                        $all[$pid] = true;
                    }
                }
            }
        }

        return [array_keys($all), $startersByMid, $playersByMid];
    }

    private function toJsonText(mixed $value): ?string
    {
        if ($value === null) {
            return null; // für NULL-Spalten
        }

        // Wenn schon JSON-Text (beginnt mit { oder [), NICHT nochmal encodieren
        if (is_string($value)) {
            $t = ltrim($value);
            if ($t !== '' && ($t[0] === '{' || $t[0] === '[')) {
                return $t;
            }
            // sonst ist es ein „normaler“ String → als JSON-String encodieren
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        // Arrays/Objekte regulär encodieren
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
