<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\Connection;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayersBatchMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperPlayersBatchHandler
{
    public function __construct(
        private readonly Connection      $db,
        private readonly LoggerInterface $logger,
    )
    {
    }

    public function __invoke(SyncSleeperPlayersBatchMessage $message): void
    {
        if ($message->players === []) {
            return;
        }

        $this->db->beginTransaction();
        try {
            foreach (array_chunk($message->players, 500) as $chunk) {
                $this->bulkUpsertPlayers($chunk);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->logger->warning('SyncSleeperPlayersBatchHandler transient error', [
                'count' => count($message->players),
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /** @param array[] $chunk Raw player arrays */
    private function bulkUpsertPlayers(array $chunk): void
    {
        if ($chunk === []) {
            return;
        }

        // 53 Spalten — Spaltenliste, Tuple und Param-Reihenfolge werden
        // gemeinsam gepflegt und muessen deckungsgleich bleiben.
        $tuple = '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_player (
    player_id,
    first_name,
    last_name,
    age,
    team,
    number,
    status,
    active,
    position,
    fantasy_positions,
    depth_chart_position,
    deptch_chart_order,
    weight,
    height,
    high_school,
    college,
    birth_date,
    birth_city,
    birth_state,
    birth_country,
    years_exp,
    espn_id,
    fantasy_data_id,
    gsis_id,
    pandascore_id,
    rotowire_id,
    rotoworld_id,
    sportradar_id,
    stats_id,
    injury_status,
    search_rank,
    swish_id,
    yahoo_id,
    full_name,
    hashtag,
    search_first_name,
    search_last_name,
    search_full_name,
    sport,
    player_shard,
    news_updated,
    injury_body_part,
    injury_notes,
    injury_start_date,
    practice_description,
    practice_participation,
    kalshi_id,
    oddsjam_id,
    opta_id,
    team_abbr,
    team_changed_at,
    metadata,
    competitions
) VALUES {$valuesSql}
ON CONFLICT (player_id) DO UPDATE SET
    first_name             = EXCLUDED.first_name,
    last_name              = EXCLUDED.last_name,
    age                    = EXCLUDED.age,
    team                   = EXCLUDED.team,
    number                 = EXCLUDED.number,
    status                 = EXCLUDED.status,
    active                 = EXCLUDED.active,
    position               = EXCLUDED.position,
    fantasy_positions      = EXCLUDED.fantasy_positions,
    depth_chart_position   = EXCLUDED.depth_chart_position,
    deptch_chart_order     = EXCLUDED.deptch_chart_order,
    weight                 = EXCLUDED.weight,
    height                 = EXCLUDED.height,
    high_school            = EXCLUDED.high_school,
    college                = EXCLUDED.college,
    birth_date             = EXCLUDED.birth_date,
    birth_city             = EXCLUDED.birth_city,
    birth_state            = EXCLUDED.birth_state,
    birth_country          = EXCLUDED.birth_country,
    years_exp              = EXCLUDED.years_exp,
    espn_id                = EXCLUDED.espn_id,
    fantasy_data_id        = EXCLUDED.fantasy_data_id,
    gsis_id                = EXCLUDED.gsis_id,
    pandascore_id          = EXCLUDED.pandascore_id,
    rotowire_id            = EXCLUDED.rotowire_id,
    rotoworld_id           = EXCLUDED.rotoworld_id,
    sportradar_id          = EXCLUDED.sportradar_id,
    stats_id               = EXCLUDED.stats_id,
    injury_status          = EXCLUDED.injury_status,
    search_rank            = EXCLUDED.search_rank,
    swish_id               = EXCLUDED.swish_id,
    yahoo_id               = EXCLUDED.yahoo_id,
    full_name              = EXCLUDED.full_name,
    hashtag                = EXCLUDED.hashtag,
    search_first_name      = EXCLUDED.search_first_name,
    search_last_name       = EXCLUDED.search_last_name,
    search_full_name       = EXCLUDED.search_full_name,
    sport                  = EXCLUDED.sport,
    player_shard           = EXCLUDED.player_shard,
    news_updated           = EXCLUDED.news_updated,
    injury_body_part       = EXCLUDED.injury_body_part,
    injury_notes           = EXCLUDED.injury_notes,
    injury_start_date      = EXCLUDED.injury_start_date,
    practice_description   = EXCLUDED.practice_description,
    practice_participation = EXCLUDED.practice_participation,
    kalshi_id              = EXCLUDED.kalshi_id,
    oddsjam_id             = EXCLUDED.oddsjam_id,
    opta_id                = EXCLUDED.opta_id,
    team_abbr              = EXCLUDED.team_abbr,
    team_changed_at        = EXCLUDED.team_changed_at,
    metadata               = EXCLUDED.metadata,
    competitions           = EXCLUDED.competitions
SQL;

        $params = [];
        foreach ($chunk as $player) {
            $params[] = $player['player_id'] ?? null;
            $params[] = $player['first_name'] ?? null;
            $params[] = $player['last_name'] ?? null;
            $params[] = isset($player['age']) ? (int)$player['age'] : null;
            $params[] = $player['team'] ?? null;
            $params[] = isset($player['number']) ? (int)$player['number'] : null;
            $params[] = $player['status'] ?? null;
            $params[] = isset($player['active']) ? ($player['active'] ? 'true' : 'false') : 'true';
            $params[] = $player['position'] ?? null;
            $params[] = isset($player['fantasy_positions']) ? json_encode($player['fantasy_positions']) : null;
            $params[] = $player['depth_chart_position'] ?? null;
            $params[] = isset($player['depth_chart_order']) ? (int)$player['depth_chart_order'] : null;
            $params[] = $player['weight'] ?? null;
            $params[] = $player['height'] ?? null;
            $params[] = $player['high_school'] ?? null;
            $params[] = $player['college'] ?? null;
            $params[] = $player['birth_date'] ?? null;
            $params[] = $player['birth_city'] ?? null;
            $params[] = $player['birth_state'] ?? null;
            $params[] = $player['birth_country'] ?? null;
            $params[] = isset($player['years_exp']) ? (int)$player['years_exp'] : null;
            $params[] = isset($player['espn_id']) ? (int)$player['espn_id'] : null;
            $params[] = isset($player['fantasy_data_id']) ? (int)$player['fantasy_data_id'] : null;
            $params[] = $player['gsis_id'] ?? null;
            $params[] = isset($player['pandascore_id']) ? (int)$player['pandascore_id'] : null;
            $params[] = isset($player['rotowire_id']) ? (int)$player['rotowire_id'] : null;
            $params[] = isset($player['rotoworld_id']) ? (int)$player['rotoworld_id'] : null;
            $params[] = $player['sportradar_id'] ?? null;
            $params[] = isset($player['stats_id']) ? (int)$player['stats_id'] : null;
            $params[] = $player['injury_status'] ?? null;
            $params[] = isset($player['search_rank']) ? (int)$player['search_rank'] : null;
            $params[] = isset($player['swish_id']) ? (int)$player['swish_id'] : null;
            $params[] = isset($player['yahoo_id']) ? (int)$player['yahoo_id'] : null;
            $params[] = $player['full_name'] ?? null;
            $params[] = $player['hashtag'] ?? null;
            $params[] = $player['search_first_name'] ?? null;
            $params[] = $player['search_last_name'] ?? null;
            $params[] = $player['search_full_name'] ?? null;
            $params[] = $player['sport'] ?? null;
            $params[] = $player['player_shard'] ?? null;
            $params[] = isset($player['news_updated']) ? (int)$player['news_updated'] : null;
            $params[] = $player['injury_body_part'] ?? null;
            $params[] = $player['injury_notes'] ?? null;
            $params[] = $player['injury_start_date'] ?? null;
            $params[] = $player['practice_description'] ?? null;
            $params[] = $player['practice_participation'] ?? null;
            $params[] = $player['kalshi_id'] ?? null;
            $params[] = $player['oddsjam_id'] ?? null;
            $params[] = $player['opta_id'] ?? null;
            $params[] = $player['team_abbr'] ?? null;
            $params[] = $player['team_changed_at'] ?? null;
            $params[] = isset($player['metadata']) ? json_encode($player['metadata']) : null;
            $params[] = isset($player['competitions']) ? json_encode($player['competitions']) : null;
        }

        $this->db->executeStatement($sql, $params);
    }
}
