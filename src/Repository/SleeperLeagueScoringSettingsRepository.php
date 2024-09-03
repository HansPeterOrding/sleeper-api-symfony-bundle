<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeagueScoringSettings;

/**
 * @method SleeperLeagueScoringSettings|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperLeagueScoringSettings|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperLeagueScoringSettings[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperLeagueScoringSettings[] findAll()
 */
class SleeperLeagueScoringSettingsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperLeagueScoringSettings::class);
    }
}
