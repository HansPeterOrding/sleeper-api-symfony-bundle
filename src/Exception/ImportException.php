<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Exception;

use RuntimeException;

class ImportException extends RuntimeException {
    public function __construct(string $message = "Unknow error on importing")
    {
        parent::__construct($message);
    }
}
