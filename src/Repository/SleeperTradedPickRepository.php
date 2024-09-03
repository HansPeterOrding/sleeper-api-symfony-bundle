<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTradedPick as SleeperTradedPickDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTradedPick as SleeperTradedPickEntity;

/**
 * @method SleeperTradedPickEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperTradedPickEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperTradedPickEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperTradedPickEntity[] findAll()
 */
class SleeperTradedPickRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperTradedPickEntity::class);
    }

    public function findByDtoOrCreateEntity(string $leagueId, string $draftId, SleeperTradedPickDto $sleeperTradedPickDto): SleeperTradedPickEntity
    {
        $sleeperTradedPick = new SleeperTradedPickEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperTradedPick->buildFindByCriteriaFromDto($leagueId, $draftId, $sleeperTradedPickDto)
            ))) {
            $sleeperTradedPick = $existingEntity;
        }

        return $sleeperTradedPick;
    }
}
