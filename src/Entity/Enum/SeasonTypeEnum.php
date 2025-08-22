<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum SeasonTypeEnum: string {
    case PRE = 'pre';
    case REGULAR = 'regular';
    case POST = 'post';
}
