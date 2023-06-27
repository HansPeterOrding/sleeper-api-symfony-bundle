<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

enum DraftStatusEnum: string
{
    case DRAFTING = 'drafting';
    case PRE_DRAFT = 'pre_draft';
    case COMPLETE = 'complete';
}
