<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum InjuryStatusEnum: string
{
    case QUESTIONABLE = 'Questionable';
    case IR = 'IR';
    case DEFAULT = '';
}
