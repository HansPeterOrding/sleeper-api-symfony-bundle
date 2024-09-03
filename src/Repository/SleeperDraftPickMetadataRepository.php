<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPickMetadata as SleeperDraftPickMetadataEntity;

/**
 * @method SleeperDraftPickMetadataEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperDraftPickMetadataEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperDraftPickMetadataEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperDraftPickMetadataEntity[] findAll()
 */
class SleeperDraftPickMetadataRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperDraftPickMetadataEntity::class);
    }
}
