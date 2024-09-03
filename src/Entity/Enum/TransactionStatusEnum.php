<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum TransactionStatusEnum: string {
    case COMPLETE = 'complete';
    case FAILED = 'failed';
    case PENDING = 'pending';
}
