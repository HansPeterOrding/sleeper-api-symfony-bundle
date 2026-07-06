<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use HansPeterOrding\SleeperApiClient\Dto\SleeperDraft as SleeperDraftDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft as SleeperDraftEntity;

/**
 * @method SleeperDraftEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperDraftEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperDraftEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperDraftEntity[] findAll()
 */
class SleeperDraftRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperDraftEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperDraftDto $sleeperDraftDto): SleeperDraftEntity
    {
        $sleeperDraft = new SleeperDraftEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperDraft->buildFindByCriteriaFromDto($sleeperDraftDto)
            ))) {
            $sleeperDraft = $existingEntity;
        }

        return $sleeperDraft;
    }

    public function pgFetchInternalId(string $draftId): int
    {
        $this->assertPostgres();
        $id = $this->db()->fetchOne(
            'SELECT id FROM public.sasb_sleeper_draft WHERE draft_id = ?',
            [$draftId]
        );
        if (!$id) {
            throw new \RuntimeException("Draft {$draftId} not found. Run draft sync first.");
        }
        return (int)$id;
    }

    public function pgFetchLeagueId(string $draftId): ?string
    {
        $this->assertPostgres();
        $leagueId = $this->db()->fetchOne(
            'SELECT league_id FROM public.sasb_sleeper_draft WHERE draft_id = ?',
            [$draftId]
        );
        return $leagueId ?: null;
    }

}
