<?php

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperNflState as SleeperNflStateDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperNflState as SleeperNflStateEntity;

/**
 * @method SleeperNflStateEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperNflStateEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperNflStateEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperNflStateEntity[] findAll()
 */
class SleeperNflStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperNflStateEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperNflStateDto $sleeperNflStateDto): SleeperNflStateEntity
    {
        $sleeperNflState = new SleeperNflStateEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperNflState->buildFindByCriteriaFromDto($sleeperNflStateDto)
            ))) {
            $sleeperNflState = $existingEntity;
        }

        return $sleeperNflState;
    }
}
