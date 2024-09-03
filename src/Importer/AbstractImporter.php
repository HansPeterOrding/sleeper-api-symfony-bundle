<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClient;
use HansPeterOrding\SleeperApiClient\ApiClient\SleeperApiClientFactory;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;

abstract class AbstractImporter {
    protected SleeperApiClient $sleeperApiClient;

    public function __construct(
        protected readonly ConverterInterface     $converter,
        protected readonly EntityManagerInterface $entityManager
    )
    {
        $this->sleeperApiClient = (new SleeperApiClientFactory())->getSleeperApiClient();
    }
}
