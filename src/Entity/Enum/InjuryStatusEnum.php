<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum InjuryStatusEnum: string {
    case QUESTIONABLE = 'Questionable';
    case DOUBTFUL = 'Doubtful';
    case IR = 'IR';
    case COV = 'COV';
    case NON_FOOTBALL_INJURY = 'Non Football Injury';
    case RESERVE_COVID19 = 'Reserve/COVID-19';
    case OUT = 'Out';
    case SUS = 'Sus';
    case PUP = 'PUP';
    case DNR = 'DNR';
    case NA = 'NA';
    case DEFAULT = '';
}
