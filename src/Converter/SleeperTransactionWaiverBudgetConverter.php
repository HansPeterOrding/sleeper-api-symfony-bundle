<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperTransactionWaiverBudget as SleeperTransactionWaiverBudgetDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransactionWaiverBudget as SleeperTransactionWaiverBudgetEntity;

class SleeperTransactionWaiverBudgetConverter
{
    public function toEntity(
        SleeperTransactionWaiverBudgetDto     $sleeperTransactionWaiverBudgetDto,
        ?SleeperTransactionWaiverBudgetEntity $sleeperTransactionWaiverBudgetEntity
    ): SleeperTransactionWaiverBudgetEntity {
        if (!$sleeperTransactionWaiverBudgetEntity) {
            $sleeperTransactionWaiverBudgetEntity = new SleeperTransactionWaiverBudgetEntity();
        }

        $sleeperTransactionWaiverBudgetEntity->setSender($sleeperTransactionWaiverBudgetDto->getSender());
        $sleeperTransactionWaiverBudgetEntity->setReceiver($sleeperTransactionWaiverBudgetDto->getReceiver());
        $sleeperTransactionWaiverBudgetEntity->setAmount($sleeperTransactionWaiverBudgetDto->getAmount());

        return $sleeperTransactionWaiverBudgetEntity;
    }
}
