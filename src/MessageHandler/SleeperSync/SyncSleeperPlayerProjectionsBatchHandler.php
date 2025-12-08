<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\Connection;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerProjections;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperStats;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerProjectionsBatch;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperPlayerProjectionsBatchHandler extends AbstractSyncStatsHandler
{
    public function __invoke(SyncSleeperPlayerProjectionsBatch $message): void
    {
        if (empty($message->projections)) {
            return;
        }

//        $this->db->beginTransaction();
        try {
            $externalPids = array_map(
                static fn(SleeperPlayerProjections $dto) => (string)$dto->getPlayerId(),
                $message->projections
            );
            $playerIdMap = $this->fetchPlayerIdMap($externalPids);

            $this->bulkUpsertProjections($message, $playerIdMap);

//            $this->db->commit();
        } catch (\Throwable $e) {
//            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param array<string,int> $playerIdMap
     */
    private function bulkUpsertProjections(SyncSleeperPlayerProjectionsBatch $message, array $playerIdMap): void
    {
        $rows   = [];
        $params = [];

        foreach ($message->projections as $dto) {
            if (!($dto instanceof SleeperPlayerProjections)) {
                continue;
            }

            $row = $this->buildRow($dto, $playerIdMap);
            if(!$row) {
                continue;
            }

            $rows[] = $row;
        }

        if ($rows === []) {
            return;
        }

        $this->upsert($rows, 'projections');
    }
}
