<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum ScoringTypeEnum: string
{
    case STANDARD = 'std';
    case PPR = 'ppr';
    case HALF_PPR = 'half_ppr';
    case TWO_QB = '2qb';
    case IDP = 'idp';
    case DYNASTY_STANDARD = 'dynasty_standard';
    case DYNASTY_PPR = 'dynasty_ppr';
    case DYNASTY_HALF_PPR = 'dynasty_half_ppr';
    case DYNASTY_TWO_QB = 'dynasty_2qb';
    case DYNASTY_IDP = 'dynasty_idp';
}
