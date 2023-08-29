<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum DraftPositionEnum: string
{
    case QB = 'QB';
    case RB = 'RB';
    case WR = 'WR';
    case TE = 'TE';
    case K = 'K';
    case DEF = 'DEF';
    case DL = 'DL';
    case LB = 'LB';
    case DB = 'DB';
    case FLEX = 'FLEX';
    case SUPER_FLEX = 'SUPER_FLEX';
    case REC_FLEX = 'REC_FLEX';
    case IDP_FLEX = 'IDP_FLEX';
    case BN = 'BN';
}
