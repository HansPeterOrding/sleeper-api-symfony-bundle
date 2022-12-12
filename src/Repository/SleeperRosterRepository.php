<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperRoster as SleeperRosterDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster as SleeperRosterEntity;

/**
 * @method SleeperRosterEntity[] findAll()
 */
class SleeperRosterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperRosterEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperRosterDto $sleeperRosterDto): SleeperRosterEntity
    {
        $sleeperRoster = new SleeperRosterEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperRoster->buildFindByCriteriaFromDto($sleeperRosterDto)
            ))) {
            $sleeperRoster = $existingEntity;
        }

        return $sleeperRoster;
    }
}
