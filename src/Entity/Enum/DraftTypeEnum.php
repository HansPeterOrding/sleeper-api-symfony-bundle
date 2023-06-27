<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum DraftTypeEnum: string
{
    case SNAKE = 'snake';
    case LINEAR = 'linear';
    case AUCTION = 'auction';
}
