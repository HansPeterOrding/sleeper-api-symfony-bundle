<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeagueSettings;

/**
 * @method SleeperLeagueSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperLeagueSettings|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperLeagueSettings[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperLeagueSettings[] findAll()
 */
class SleeperLeagueSettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperLeagueSettings::class);
    }
}
