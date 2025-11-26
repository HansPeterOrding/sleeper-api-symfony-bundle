<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjections as SleeperPlayerProjectionsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayerProjections as SleeperPlayerProjectionsEntity;

/**
 * @method SleeperPlayerProjectionsEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayerProjectionsEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayerProjectionsEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayerProjectionsEntity[] findAll()
 */
class SleeperPlayerProjectionsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperPlayerProjectionsEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperPlayerProjectionsDto $sleeperPlayerProjectionsDto): SleeperPlayerProjectionsEntity
    {
        $sleeperPlayerProjections = new SleeperPlayerProjectionsEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayerProjections->buildFindByCriteriaFromDto($sleeperPlayerProjectionsDto)
            ))) {
            $sleeperPlayerProjections = $existingEntity;
        }

        return $sleeperPlayerProjections;
    }
}
