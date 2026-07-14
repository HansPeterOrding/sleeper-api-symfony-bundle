<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\RetryableException;
use Doctrine\DBAL\Exception\ServerException;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerStatsBatch;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerStatsRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

/**
 * Thin by design: all SQL lives in SleeperPlayerStatsRepository::pgBulkUpsertStats()
 * (pg-prefixed repository method convention). This handler only unpacks the
 * message, delegates, and classifies failures for Messenger.
 *
 * Failure classification — identical to
 * SyncSleeperPlayerProjectionsBatchHandler (see its docblock for the full
 * rationale): deterministic failures (\Error from broken payloads/DTOs, DBAL
 * ServerException from schema drift) go straight to the failure transport
 * instead of burning the retry budget on identical input; transient DB errors
 * (RetryableException first — DeadlockException extends ServerException —
 * plus ConnectionException) are logged and rethrown for Messenger's retry;
 * everything else stays uncaught → default retry.
 */
#[AsMessageHandler]
final class SyncSleeperPlayerStatsBatchHandler
{
    public function __construct(
        private readonly SleeperPlayerStatsRepository $sleeperPlayerStatsRepository,
        private readonly LoggerInterface              $logger,
    )
    {
    }

    public function __invoke(SyncSleeperPlayerStatsBatch $message): void
    {
        if ($message->stats === []) {
            return;
        }

        try {
            $this->sleeperPlayerStatsRepository->pgBulkUpsertStats(
                $message->season,
                $message->week,
                $message->stats,
            );
        } catch (RetryableException|ConnectionException $e) {
            $this->logger->warning('SyncSleeperPlayerStatsBatchHandler transient DB error', [
                'season' => $message->season,
                'week' => $message->week,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Error|ServerException $e) {
            $this->logger->critical('SyncSleeperPlayerStatsBatchHandler deterministic error — not retrying', [
                'season' => $message->season,
                'week' => $message->week,
                'batchSize' => count($message->stats),
                'error' => $e->getMessage(),
            ]);
            throw new UnrecoverableMessageHandlingException($e->getMessage(), previous: $e);
        }
    }
}
