<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Util;

use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule;
use HansPeterOrding\SleeperApiSymfonyBundle\Dto\LeagueSchedule\Rank;

class ScheduleUtils
{
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
    public static function sortRanks(array $ranks): array
    {
        usort($ranks, function (Rank $a, Rank $b) {
            if ($a->getGamesWon() !== $b->getGamesWon()) {
                return -1 * ($a->getGamesWon() <=> $b->getGamesWon());
            }

            if ($a->getGamesDraw() !== $b->getGamesDraw()) {
                return -1 * ($a->getGamesDraw() <=> $b->getGamesDraw());
            }

            if ($a->getPointsFor() !== $b->getPointsFor()) {
                return -1 * ($a->getPointsFor() <=> $b->getPointsFor());
            }

            return -1 * ($b->getPointsAgainst() <=> $a->getPointsAgainst());
        });

        return $ranks;
    }
}
