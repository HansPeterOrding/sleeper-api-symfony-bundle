<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperDraft as SleeperDraftDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft as SleeperDraftEntity;

/**
 * @method SleeperDraftEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperDraftEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperDraftEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperDraftEntity[] findAll()
 */
class SleeperDraftRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperDraftEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperDraftDto $sleeperDraftDto): SleeperDraftEntity
    {
        $sleeperDraft = new SleeperDraftEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperDraft->buildFindByCriteriaFromDto($sleeperDraftDto)
            ))) {
            $sleeperDraft = $existingEntity;
        }

        return $sleeperDraft;
    }
}
