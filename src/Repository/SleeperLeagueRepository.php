<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperLeague as SleeperLeagueDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague as SleeperLeagueEntity;

/**
 * @method SleeperLeagueEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperLeagueEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperLeagueEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperLeagueEntity[] findAll()
 */
class SleeperLeagueRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperLeagueEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperLeagueDto $sleeperLeagueDto): SleeperLeagueEntity
    {
        $sleeperLeague = new SleeperLeagueEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperLeague->buildFindByCriteriaFromDto($sleeperLeagueDto)
            ))) {
            $sleeperLeague = $existingEntity;
        }

        return $sleeperLeague;
    }
}
