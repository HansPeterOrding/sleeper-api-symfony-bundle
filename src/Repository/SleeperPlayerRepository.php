<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\ArrayParameterType;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayer as SleeperPlayerDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayer as SleeperPlayerEntity;

/**
 * @method SleeperPlayerEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayerEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayerEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayerEntity[] findAll()
 */
class SleeperPlayerRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

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

    /** @return array<string,int> [playerId => internal id] */
    public function pgFetchPlayerIdMap(array $playerIds): array
    {
        $this->assertPostgres();
        $playerIds = array_values(array_unique(array_filter(array_map('strval', $playerIds))));
        if ($playerIds === []) {
            return [];
        }
        $map = [];
        foreach (array_chunk($playerIds, 1000) as $chunk) {
            $rows = $this->db()->fetchAllAssociative(
                'SELECT player_id, id FROM public.sasb_sleeper_player WHERE player_id IN (?)',
                [$chunk],
                [ArrayParameterType::STRING]
            );
            foreach ($rows as $r) {
                $map[(string)$r['player_id']] = (int)$r['id'];
            }
        }
        return $map;
    }

}
