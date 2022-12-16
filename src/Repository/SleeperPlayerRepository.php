<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayer as SleeperPlayerDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayer as SleeperPlayerEntity;

/**
 * @method SleeperPlayerEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayerEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayerEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayerEntity[] findAll()
 */
class SleeperPlayerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperPlayerEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperPlayerDto $sleeperPlayerDto): SleeperPlayerEntity
    {
        $sleeperPlayer = new SleeperPlayerEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayer->buildFindByCriteriaFromDto($sleeperPlayerDto)
            ))) {
            $sleeperPlayer = $existingEntity;
        }

        return $sleeperPlayer;
    }
}
