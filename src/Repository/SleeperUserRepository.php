<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperUser as SleeperUserDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser as SleeperUserEntity;

class SleeperUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperUserEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperUserDto $sleeperUserDto): SleeperUserEntity
    {
        $sleeperUser = new SleeperUserEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperUser->buildFindByCriteriaFromDto($sleeperUserDto)
            ))) {
            $sleeperUser = $existingEntity;
        }

        return $sleeperUser;
    }
}
