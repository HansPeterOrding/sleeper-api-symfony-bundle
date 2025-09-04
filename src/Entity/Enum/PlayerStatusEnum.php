<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum PlayerStatusEnum: string {
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case INJURED_RESERVE = 'Injured Reserve';
    case NON_FOOTBALL_INJURY = 'Non Football Injury';
    case PHYSICALLY_UNABLE_TO_PERFORM = 'Physically Unable to Perform';
    case SUSPENDED = 'Suspended';
    case RESERVE_COVID19 = 'Reserve/COVID-19';
    case PRACTICE_SQUAD = 'Practice Squad';
    case DID_NOT_REPORT = 'Did Not Report';
}
