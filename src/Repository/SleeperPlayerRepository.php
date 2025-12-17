<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayer as SleeperPlayerDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayer as SleeperPlayerEntity;

/**
 * @method SleeperPlayerEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayerEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayerEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayerEntity[] findAll()
 */
class SleeperPlayerRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperPlayerEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperPlayerDto $sleeperPlayerDto): SleeperPlayerEntity
    {
        $sleeperPlayer = new SleeperPlayerEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayer->buildFindByCriteriaFromDto($sleeperPlayerDto)
            ))) {
            $sleeperPlayer = $existingEntity;
        }

        return $sleeperPlayer;
    }

    public function findRelevantPlayers(string $season)
    {
        $qb = $this->createQueryBuilder('p');
//        $qb->andWhere($qb->expr()->eq('p.active', ':active'))->setParameter('active', true);
//        $qb->andWhere($qb->expr()->isNotNull('p.team'));
//        $qb->andWhere($qb->expr()->in('p.position', ':position'))->setParameter('position', ['QB', 'RB', 'DEF', 'FB', 'K', 'WR', 'TE', 'K/P']);
        $qb->andWhere($qb->expr()->in('p.position', ':position'))->setParameter('position', ['TE']);

        $qb->addOrderBy(new OrderBy('p.position', 'ASC'));
        $qb->addOrderBy(new OrderBy('p.team', 'ASC'));
        $qb->addOrderBy(new OrderBy('p.lastName', 'ASC'));
        $qb->addOrderBy(new OrderBy('p.firstName', 'ASC'));

        return $qb->getQuery()->getResult();
    }
}
