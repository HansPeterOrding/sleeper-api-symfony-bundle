<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class SleeperPlayoffMatchupSource {
    #[ORM\Column(nullable: true)]
    private ?int $w = null;

    #[ORM\Column(nullable: true)]
    private ?int $l = null;

    public function getW(): ?int
    {
        return $this->w;
    }

    public function setW(?int $w): SleeperPlayoffMatchupSource
    {
        $this->w = $w;
        return $this;
    }

    public function getL(): ?int
    {
        return $this->l;
    }

    public function setL(?int $l): SleeperPlayoffMatchupSource
    {
        $this->l = $l;
        return $this;
    }
}
