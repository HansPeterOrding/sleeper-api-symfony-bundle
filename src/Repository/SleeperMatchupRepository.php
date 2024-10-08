<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperMatchup as SleeperMatchupDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperMatchup as SleeperMatchupEntity;

/**
 * @method SleeperMatchupEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperMatchupEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperMatchupEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperMatchupEntity[] findAll()
 */
class SleeperMatchupRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperMatchupEntity::class);
    }

    public function findByDtoOrCreateEntity(string $leagueId, int $week, SleeperMatchupDto $sleeperMatchupDto): SleeperMatchupEntity
    {
        $sleeperMatchup = new SleeperMatchupEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperMatchup->buildFindByCriteriaFromDto($leagueId, $week, $sleeperMatchupDto)
            ))) {
            $sleeperMatchup = $existingEntity;
        }

        return $sleeperMatchup;
    }
}
