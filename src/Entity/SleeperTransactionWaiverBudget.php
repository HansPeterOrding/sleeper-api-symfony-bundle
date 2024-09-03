<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\ScoringTypeEnum;

#[ORM\Embeddable]
class SleeperTransactionWaiverBudget
{
    #[ORM\Column]
    private int $sender;

    #[ORM\Column]
    private int $receiver;

    #[ORM\Column]
    private int $amount;

    public function getSender(): int
    {
        return $this->sender;
    }

    public function setSender(int $sender): void
    {
        $this->sender = $sender;
    }

    public function getReceiver(): int
    {
        return $this->receiver;
    }

    public function setReceiver(int $receiver): void
    {
        $this->receiver = $receiver;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }
}
