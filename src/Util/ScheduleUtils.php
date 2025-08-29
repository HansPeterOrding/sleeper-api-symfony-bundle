<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Util;

use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\Rank;

class ScheduleUtils {
    const ORDER_GAMES_WON = 'gamesWon';
    const ORDER_GAMES_DRAW = 'gamesDraw';
    const ORDER_GAMES_LOST = 'gamesLost';
    const ORDER_POINTS_FOR = 'pointFor';
    const ORDER_POINTS_AGAINST = 'pointsAgainst';

    public static array $defaultCriteriaOrder = [
        self::ORDER_GAMES_WON,
        self::ORDER_GAMES_DRAW,
        self::ORDER_POINTS_FOR,
        self::ORDER_POINTS_AGAINST,
    ];

    /**
     * @param array<int, LeagueSchedule> $schedules
     * @return array<int, LeagueSchedule\Rank>
     */
    public static function combineScheduleRanks(array $schedules): array
    {
        $ranks = [];
        foreach (array_map(function (LeagueSchedule $schedule) {
            return $schedule->getStandings()->getRanks();
        }, $schedules) as $rankList) {
            $ranks = array_merge($ranks, $rankList);
        }

        return $ranks;
    }

    /**
     * @param array<int, Rank> $ranks
     * @return array<int, Rank>
     */
    public function sortRanks(array $ranks, ?array $criteriaOrder = null): array
    {
        if(!$criteriaOrder) {
            $criteriaOrder = static::$defaultCriteriaOrder;
        }

        usort($ranks, function (Rank $a, Rank $b) use ($criteriaOrder) {
            foreach($criteriaOrder as $criteria) {
                switch($criteria) {
                    case self::ORDER_POINTS_FOR:
                        if ($a->getPointsFor() !== $b->getPointsFor()) {
                            return -1 * ($a->getPointsFor() <=> $b->getPointsFor());
                        }
                        break;
                    case self::ORDER_POINTS_AGAINST:
                        if ($a->getPointsAgainst() !== $b->getPointsAgainst()) {
                            return -1 * ($a->getPointsAgainst() <=> $b->getPointsAgainst());
                        }
                        break;
                    case self::ORDER_GAMES_WON:
                        if ($a->getGamesWon() !== $b->getGamesWon()) {
                            return -1 * ($a->getGamesWon() <=> $b->getGamesWon());
                        }
                        break;
                    case self::ORDER_GAMES_DRAW:
                        if ($a->getGamesDraw() !== $b->getGamesDraw()) {
                            return -1 * ($a->getGamesDraw() <=> $b->getGamesDraw());
                        }
                        break;
                    case self::ORDER_GAMES_LOST:
                        if ($a->getGamesLost() !== $b->getGamesLost()) {
                            return $a->getGamesLost() <=> $b->getGamesLost();
                        }
                        break;

                    default:
                        return 0;
                }
            }
            return 0;
        });

        return $ranks;
    }
}
