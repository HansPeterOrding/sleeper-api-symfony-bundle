<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftMetadata as SleeperDraftMetadataEntity;

/**
 * @method SleeperDraftMetadataEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperDraftMetadataEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperDraftMetadataEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperDraftMetadataEntity[] findAll()
 */
class SleeperDraftMetadataRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperDraftMetadataEntity::class);
    }
}
