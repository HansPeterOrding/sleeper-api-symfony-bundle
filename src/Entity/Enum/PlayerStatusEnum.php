<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum PlayerStatusEnum:string
{
    case ACTIVE = 'Active';
    case INACTIVE = 'Inactive';
}
