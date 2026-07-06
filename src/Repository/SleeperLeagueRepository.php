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
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

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

    /**
     * Internal PK for a Sleeper league id. Throws if the league row does not
     * exist yet — callers must persist the league core first.
     */
    public function pgFetchInternalId(string $leagueId): int
    {
        $this->assertPostgres();
        $id = $this->db()->fetchOne(
            'SELECT id FROM public.sasb_sleeper_league WHERE league_id = ?',
            [$leagueId]
        );
        if (!$id) {
            throw new \RuntimeException("League {$leagueId} not found. Run league sync first.");
        }
        return (int)$id;
    }

    public function pgFetchDraftId(string $leagueId): ?string
    {
        $this->assertPostgres();
        $draftId = $this->db()->fetchOne(
            'SELECT draft_id FROM public.sasb_sleeper_league WHERE league_id = ?',
            [$leagueId]
        );
        return $draftId ?: null;
    }

    public function pgFetchPlayoffWeekStart(string $leagueId): int
    {
        $this->assertPostgres();
        $week = $this->db()->fetchOne(
            'SELECT settings_playoff_week_start FROM public.sasb_sleeper_league WHERE league_id = ?',
            [$leagueId]
        );
        return (int)($week ?? 15);
    }

}
