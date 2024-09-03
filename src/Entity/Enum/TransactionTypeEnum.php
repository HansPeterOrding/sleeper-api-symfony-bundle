<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum TransactionTypeEnum: string
{
    case WAIVER = 'waiver';
    case FREE_AGENT = 'free_agent';
    case TRADE = 'trade';
}
