<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum PlayerStatusEnum: string {
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
    case INJURED_RESERVE = 'Injured Reserve';
    case NON_FOOTBALL_INJURY = 'Non Football Injury';
    case RESERVE_COVID19 = 'Reserve/COVID-19';
}
