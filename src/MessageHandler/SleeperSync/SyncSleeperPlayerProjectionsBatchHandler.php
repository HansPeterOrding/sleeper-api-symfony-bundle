<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\MessageHandler\SleeperSync;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\RetryableException;
use Doctrine\DBAL\Exception\ServerException;
use HansPeterOrding\SleeperApiSymfonyBundle\Message\SleeperSync\SyncSleeperPlayerProjectionsBatch;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerProjectionsRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;

/**
 * Thin by design: all SQL lives in
 * SleeperPlayerProjectionsRepository::pgBulkUpsertProjections() (pg-prefixed
 * repository method convention). This handler only unpacks the message,
 * delegates, and classifies failures for Messenger.
 *
 * Failure classification (the message payload carries the full DTO batch, so
 * redelivery replays the IDENTICAL input — deterministic failures can never
 * succeed on retry and would otherwise burn the full retry budget with
 * backoff before reaching the failure transport, as happened with the
 * uninitialized-$date DTO bug):
 *
 *  - \Error (TypeError, uninitialized typed property, ...) during row
 *    building: broken payload/DTO — unrecoverable, straight to the failure
 *    transport (replayable there once the client/bundle fix is deployed).
 *  - DBAL ServerException (undefined column, constraint violation, ...):
 *    schema drift — equally deterministic per deploy, unrecoverable.
 *  - DBAL RetryableException (deadlock, lock timeout — caught FIRST, since
 *    DeadlockException extends ServerException) and ConnectionException
 *    (incl. ConnectionLost): transient — logged, rethrown, Messenger retries.
 *  - Anything else stays uncaught → Messenger's default retry (conservative).
 */
#[AsMessageHandler]
final class SyncSleeperPlayerProjectionsBatchHandler
{
    public function __construct(
        private readonly SleeperPlayerProjectionsRepository $sleeperPlayerProjectionsRepository,
        private readonly LoggerInterface                    $logger,
    )
    {
    }

    public function __invoke(SyncSleeperPlayerProjectionsBatch $message): void
    {
        if ($message->projections === []) {
            return;
        }

        try {
            $this->sleeperPlayerProjectionsRepository->pgBulkUpsertProjections(
                $message->season,
                $message->week,
                $message->projections,
            );
        } catch (RetryableException|ConnectionException $e) {
            $this->logger->warning('SyncSleeperPlayerProjectionsBatchHandler transient DB error', [
                'season' => $message->season,
                'week' => $message->week,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } catch (\Error|ServerException $e) {
            $this->logger->critical('SyncSleeperPlayerProjectionsBatchHandler deterministic error — not retrying', [
                'season' => $message->season,
                'week' => $message->week,
                'batchSize' => count($message->projections),
                'error' => $e->getMessage(),
            ]);
            throw new UnrecoverableMessageHandlingException($e->getMessage(), previous: $e);
        }
    }
}
