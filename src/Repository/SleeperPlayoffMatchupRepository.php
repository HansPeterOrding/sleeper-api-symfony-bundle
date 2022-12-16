<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayoffMatchup as SleeperPlayoffMatchupDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup as SleeperPlayoffMatchupEntity;

/**
 * @method SleeperPlayoffMatchup|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayoffMatchup|null  findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayoffMatchup[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayoffMatchup[] findAll()
 */
class SleeperPlayoffMatchupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperPlayoffMatchupEntity::class);
    }

    public function findByDtoOrCreateEntity(string $leagueId, string $branch, SleeperPlayoffMatchupDto $sleeperPlayoffMatchupDto): SleeperPlayoffMatchupEntity
    {
        $sleeperPlayoffMatchup = new SleeperPlayoffMatchupEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayoffMatchup->buildFindByCriteriaFromDto($leagueId, $branch, $sleeperPlayoffMatchupDto)
            ))) {
            $sleeperPlayoffMatchup = $existingEntity;
        }

        return $sleeperPlayoffMatchup;
    }
}
