<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperTransaction as SleeperTransactionDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransaction;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransaction as SleeperTransactionEntity;

/**
 * @method SleeperTransactionEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperTransactionEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperTransactionEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperTransactionEntity[] findAll()
 */
class SleeperTransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperTransactionEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperTransactionDto $sleeperTransactionDto): SleeperTransactionEntity
    {
        $sleeperTransaction = new SleeperTransaction();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperTransaction->buildFindByCriteriaFromDto($sleeperTransactionDto)
            ))) {
            $sleeperTransaction = $existingEntity;
        }

        return $sleeperTransaction;
    }
}
