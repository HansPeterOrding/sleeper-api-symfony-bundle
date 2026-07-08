<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjections as SleeperPlayerProjectionsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayerProjections as SleeperPlayerProjectionsEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PgPlayerStatsUpsertTrait;

/**
 * @method SleeperPlayerProjectionsEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayerProjectionsEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayerProjectionsEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayerProjectionsEntity[] findAll()
 */
class SleeperPlayerProjectionsRepository extends ServiceEntityRepository
{
    use PgPlayerStatsUpsertTrait;

    private const string TABLE = 'sasb_sleeper_player_projections';

    /** Matches the ORM UniqueConstraint('uniq_sasb_sleeper_player_projections') column order exactly. */
    private const array CONFLICT_COLUMNS = ['season', 'week', 'player_id'];

    public function __construct(
        ManagerRegistry                          $registry,
        private readonly SleeperPlayerRepository $sleeperPlayerRepository,
    )
    {
        parent::__construct($registry, SleeperPlayerProjectionsEntity::class);
    }

    /**
     * Kept for the five-layer / findByDtoOrCreateEntity convention used
     * elsewhere in the bundle. NOT used by the sync write path any more —
     * see pgBulkUpsertProjections() below, which is what
     * SyncSleeperPlayerProjectionsBatchHandler actually calls.
     */
    public function findByDtoOrCreateEntity(SleeperPlayerProjectionsDto $sleeperPlayerProjectionsDto): SleeperPlayerProjectionsEntity
    {
        $sleeperPlayerProjections = new SleeperPlayerProjectionsEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayerProjections->buildFindByCriteriaFromDto($sleeperPlayerProjectionsDto)
            ))) {
            $sleeperPlayerProjections = $existingEntity;
        }

        return $sleeperPlayerProjections;
    }

    /**
     * Bulk write path for SyncSleeperPlayerProjectionsBatchHandler. Multi-VALUES
     * INSERT ... ON CONFLICT (season, week, player_id) DO UPDATE, chunked and
     * transactional (see PgPlayerStatsUpsertTrait::pgUpsertRows()).
     *
     * $season and $week are the batch's own values (from SyncSleeperPlayerProjectionsBatch),
     * NOT read from each DTO — see buildRow() docblock for why.
     *
     * @param SleeperPlayerProjectionsDto[] $projectionsDtos
     */
    public function pgBulkUpsertProjections(string $season, ?int $week, array $projectionsDtos): void
    {
        $this->assertPostgres();
        if ($projectionsDtos === []) {
            return;
        }

        $externalPids = array_map(
            static fn(SleeperPlayerProjectionsDto $dto): string => (string)$dto->getPlayerId(),
            $projectionsDtos,
        );
        $playerIdMap = $this->sleeperPlayerRepository->pgFetchPlayerIdMap($externalPids);

        $rows = [];
        foreach ($projectionsDtos as $dto) {
            if (!$dto instanceof SleeperPlayerProjectionsDto) {
                continue;
            }
            $row = $this->buildRow($dto, $playerIdMap, $season, $week);
            if ($row !== null) {
                $rows[] = $row;
            }
        }

        $this->pgUpsertRows(self::TABLE, $rows, self::CONFLICT_COLUMNS);
    }
}
