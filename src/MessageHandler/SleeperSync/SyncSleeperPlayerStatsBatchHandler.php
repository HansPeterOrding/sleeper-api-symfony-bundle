<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\Connection;
use HansPeterOrding\SleeperApiClient\Dto\SleeperPlayerStats;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerStatsBatch;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SyncSleeperPlayerStatsBatchHandler extends AbstractSyncStatsHandler
{
    public function __invoke(SyncSleeperPlayerStatsBatch $message): void
    {
        if (empty($message->stats)) {
            return;
        }

//        $this->db->beginTransaction();
        try {
            $externalPids = array_map(
                static fn(SleeperPlayerStats $dto) => (string)$dto->getPlayerId(),
                $message->stats
            );
            $playerIdMap = $this->fetchPlayerIdMap($externalPids);

            $this->bulkUpsertStats($message, $playerIdMap);

//            $this->db->commit();
        } catch (\Throwable $e) {
//            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param array<string,int> $playerIdMap
     */
    private function bulkUpsertStats(SyncSleeperPlayerStatsBatch $message, array $playerIdMap): void
    {
        $rows   = [];
        $params = [];

        foreach ($message->stats as $dto) {
            if (!$dto instanceof SleeperPlayerStats) {
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

        $this->upsert($rows);
    }
}
