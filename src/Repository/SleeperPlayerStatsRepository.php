<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats as SleeperPlayerStatsDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayerStats as SleeperPlayerStatsEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PgPlayerStatsUpsertTrait;

/**
 * @method SleeperPlayerStatsEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperPlayerStatsEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperPlayerStatsEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperPlayerStatsEntity[] findAll()
 */
class SleeperPlayerStatsRepository extends ServiceEntityRepository
{
    use PgPlayerStatsUpsertTrait;

    private const string TABLE = 'sasb_sleeper_player_stats';

    /** Matches the ORM UniqueConstraint('uniq_sasb_sleeper_player_stats') column order exactly. */
    private const array CONFLICT_COLUMNS = ['season', 'week', 'player_id'];

    public function __construct(
        ManagerRegistry                          $registry,
        private readonly SleeperPlayerRepository $sleeperPlayerRepository,
    )
    {
        parent::__construct($registry, SleeperPlayerStatsEntity::class);
    }

    /**
     * Kept for the five-layer / findByDtoOrCreateEntity convention used
     * elsewhere in the bundle. NOT used by the sync write path any more —
     * see pgBulkUpsertStats() below, which is what
     * SyncSleeperPlayerStatsBatchHandler actually calls.
     */
    public function findByDtoOrCreateEntity(SleeperPlayerStatsDto $sleeperPlayerStatsDto): SleeperPlayerStatsEntity
    {
        $sleeperPlayerStats = new SleeperPlayerStatsEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperPlayerStats->buildFindByCriteriaFromDto($sleeperPlayerStatsDto)
            ))) {
            $sleeperPlayerStats = $existingEntity;
        }

        return $sleeperPlayerStats;
    }

    /**
     * Bulk write path for SyncSleeperPlayerStatsBatchHandler. Multi-VALUES
     * INSERT ... ON CONFLICT (season, week, player_id) DO UPDATE, chunked and
     * transactional (see PgPlayerStatsUpsertTrait::pgUpsertRows()).
     *
     * $season and $week are the batch's own values (from SyncSleeperPlayerStatsBatch),
     * NOT read from each DTO — see buildRow() docblock for why.
     *
     * @param SleeperPlayerStatsDto[] $statsDtos
     */
    public function pgBulkUpsertStats(string $season, ?int $week, array $statsDtos): void
    {
        $this->assertPostgres();
        if ($statsDtos === []) {
            return;
        }

        $externalPids = array_map(
            static fn(SleeperPlayerStatsDto $dto): string => (string)$dto->getPlayerId(),
            $statsDtos,
        );
        $playerIdMap = $this->sleeperPlayerRepository->pgFetchPlayerIdMap($externalPids);

        $rows = [];
        foreach ($statsDtos as $dto) {
            if (!$dto instanceof SleeperPlayerStatsDto) {
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
