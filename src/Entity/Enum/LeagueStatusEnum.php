<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum LeagueStatusEnum: string
{
    case PRE_DRAFT = 'pre_draft';
    case DRAFTING = 'drafting';
    case IN_SEASON = 'in_season';
}
