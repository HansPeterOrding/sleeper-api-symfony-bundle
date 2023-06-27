<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperDraftPick as SleeperDraftPickDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick as SleeperDraftPickEntity;

/**
 * @method SleeperDraftPickEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperDraftPickEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperDraftPickEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperDraftPickEntity[] findAll()
 */
class SleeperDraftPickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperDraftPickEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperDraftPickDto $sleeperDraftPickDto): SleeperDraftPickEntity
    {
        $sleeperDraftPick = new SleeperDraftPickEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperDraftPick->buildFindByCriteriaFromDto($sleeperDraftPickDto)
            ))) {
            $sleeperDraftPick = $existingEntity;
        }

        return $sleeperDraftPick;
    }
}
