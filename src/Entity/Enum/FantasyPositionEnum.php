<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum FantasyPositionEnum: string {
    case QB = 'QB';
    case RB = 'RB';
    case WR = 'WR';
    case TE = 'TE';
    case K = 'K';
    case DEF = 'DEF';
    case DL = 'DL';
    case LB = 'LB';
    case DB = 'DB';
}
