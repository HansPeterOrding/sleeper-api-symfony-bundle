<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class HpoSleeperApiSymfonyBundle extends Bundle {
    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}
