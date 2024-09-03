<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftSettings as SleeperDraftSettingsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftSettings as SleeperDraftSettingsEntity;

/**
 * @method SleeperDraftSettingsEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperDraftSettingsEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperDraftSettingsEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperDraftSettingsEntity[] findAll()
 */
class SleeperDraftSettingsRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperDraftSettingsEntity::class);
    }
}
