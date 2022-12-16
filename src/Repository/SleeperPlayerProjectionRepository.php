<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjection as SleeperPlayerProjectionDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayerProjection as SleeperPlayerProjectionEntity;

/**
 * @method SleeperPlayerProjectionEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayerProjectionEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayerProjectionEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayerProjectionEntity[] findAll()
 */
class SleeperPlayerProjectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperPlayerProjectionEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperPlayerProjectionDto $sleeperPlayerProjectionDto): SleeperPlayerProjectionEntity
    {
        $sleeperPlayerProjection = new SleeperPlayerProjectionEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayerProjection->buildFindByCriteriaFromDto($sleeperPlayerProjectionDto)
            ))) {
            $sleeperPlayerProjection = $existingEntity;
        }

        return $sleeperPlayerProjection;
    }
}
