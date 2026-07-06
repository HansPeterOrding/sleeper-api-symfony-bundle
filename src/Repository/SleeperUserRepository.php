<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\ArrayParameterType;
use HansPeterOrding\SleeperApiClient\Dto\SleeperUser as SleeperUserApiDto;
use HansPeterOrding\SleeperApiClient\Dto\SleeperUser as SleeperUserDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser as SleeperUserEntity;

/**
 * @method SleeperUserEntity|null find($id, $lockMode = null, $lockVersion = null)
 * @method SleeperUserEntity|null findOneBy(array $criteria, ?array $orderBy = null)
 * @method SleeperUserEntity[] findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
 * @method SleeperUserEntity[] findAll()
 */
class SleeperUserRepository extends ServiceEntityRepository
{
    use \HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits\PostgresPlatformAssertionTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SleeperUserEntity::class);
    }

    public function findByDtoOrCreateEntity(SleeperUserDto $sleeperUserDto): SleeperUserEntity
    {
        $sleeperUser = new SleeperUserEntity();
        if (null !== ($existingEntity = $this->findOneBy(
                $sleeperUser->buildFindByCriteriaFromDto($sleeperUserDto)
            ))) {
            $sleeperUser = $existingEntity;
        }

        return $sleeperUser;
    }

    /**
     * PostgreSQL bulk upsert of Sleeper users. Chunked internally; does NOT open
     * a transaction — callers own atomicity.
     *
     * @param SleeperUserApiDto[] $users
     */
    public function pgBulkUpsertUsers(array $users, int $chunkSize = 200): void
    {
        $this->assertPostgres();
        foreach (array_chunk($users, $chunkSize) as $chunk) {
            $this->pgUpsertUserChunk($chunk);
        }
    }

    /** @return array<string,int> [userId => internal id] */
    public function pgFetchUserIdMap(array $userIds): array
    {
        $this->assertPostgres();
        $userIds = array_values(array_filter(array_unique($userIds)));
        if ($userIds === []) {
            return [];
        }
        $rows = $this->db()->fetchAllAssociative(
            'SELECT user_id, id FROM public.sasb_sleeper_user WHERE user_id IN (?)',
            [$userIds],
            [ArrayParameterType::STRING]
        );
        $map = [];
        foreach ($rows as $r) {
            $map[$r['user_id']] = (int)$r['id'];
        }
        return $map;
    }

    /** @param SleeperUserApiDto[] $chunk */
    private function pgUpsertUserChunk(array $chunk): void
    {
        if ($chunk === []) {
            return;
        }

        $tuple = '(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_user (
    user_id, username, display_name, avatar, real_name,
    is_bot, email, phone, pending, solicitable,
    verification, token, summoner_name, summoner_region,
    metadata_team_name
) VALUES {$valuesSql}
ON CONFLICT (user_id) DO UPDATE SET
    username         = EXCLUDED.username,
    display_name     = EXCLUDED.display_name,
    avatar           = EXCLUDED.avatar,
    real_name        = EXCLUDED.real_name,
    is_bot           = EXCLUDED.is_bot,
    email            = EXCLUDED.email,
    phone            = EXCLUDED.phone,
    pending          = EXCLUDED.pending,
    solicitable      = EXCLUDED.solicitable,
    verification     = EXCLUDED.verification,
    token            = EXCLUDED.token,
    summoner_name    = EXCLUDED.summoner_name,
    summoner_region  = EXCLUDED.summoner_region,
    metadata_team_name = EXCLUDED.metadata_team_name
SQL;

        $params = [];
        foreach ($chunk as $dto) {
            $params[] = $dto->getUserId();
            $params[] = $dto->getUsername();
            $params[] = $dto->getDisplayName();
            $params[] = $dto->getAvatar();
            $params[] = $dto->getRealName();
            $params[] = $dto->getIsBot() ? 'true' : 'false';
            $params[] = $dto->getEmail();
            $params[] = $dto->getPhone();
            $params[] = $dto->getPending();
            $params[] = $dto->getSolicitable();
            $params[] = $dto->getVerification();
            $params[] = $dto->getToken();
            $params[] = $dto->getSummonerName();
            $params[] = $dto->getSummonerRegion();
            $params[] = $dto->getMetadata()?->getTeamName();
        }

        $this->db()->executeStatement($sql, $params);
    }

}
