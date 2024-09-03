<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats as SleeperPlayerStatsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayerStats as SleeperPlayerStatsEntity;

/**
 * @method SleeperPlayerStatsEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayerStatsEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayerStatsEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayerStatsEntity[] findAll()
 */
class SleeperPlayerStatsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperPlayerStatsEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperPlayerStatsDto $sleeperPlayerStatsDto): SleeperPlayerStatsEntity
    {
        $sleeperPlayerStats = new SleeperPlayerStatsEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayerStats->buildFindByCriteriaFromDto($sleeperPlayerStatsDto)
            ))) {
            $sleeperPlayerStats = $existingEntity;
        }

        return $sleeperPlayerStats;
    }
}
