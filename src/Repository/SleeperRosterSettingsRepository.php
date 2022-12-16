<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRosterSettings;

/**
 * @method SleeperRosterSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperRosterSettings|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperRosterSettings[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperRosterSettings[] findAll()
 */
class SleeperRosterSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperRosterSettings::class);
    }
}
