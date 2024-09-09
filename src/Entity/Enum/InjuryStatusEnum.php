<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum InjuryStatusEnum: string {
    case QUESTIONABLE = 'Questionable';
    case DOUBTFUL = 'Doubtful';
    case IR = 'IR';
    case OUT = 'Out';
    case SUS = 'Sus';
    case PUP = 'PUP';
    case DNR = 'DNR';
    case NA = 'NA';
    case DEFAULT = '';
}
