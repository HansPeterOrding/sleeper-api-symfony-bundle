<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class SleeperTransactionSettings
{
    #[ORM\Column(nullable: true)]
    private ?int $waiverBid = null;

    #[ORM\Column(nullable: true)]
    private ?int $seq = null;

    #[ORM\Column(nullable: true)]
    private ?int $priority = null;

    public function getWaiverBid(): ?int
    {
        return $this->waiverBid;
    }

    public function setWaiverBid(?int $waiverBid): void
    {
        $this->waiverBid = $waiverBid;
    }

    public function getSeq(): ?int
    {
        return $this->seq;
    }

    public function setSeq(?int $seq): void
    {
        $this->seq = $seq;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }
}
