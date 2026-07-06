<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Repository\Traits;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;

/**
 * Shared plumbing for the pg*() bulk-write/lookup repository methods.
 *
 * Every pg-prefixed method is PostgreSQL-ONLY (ON CONFLICT upserts, ::json
 * casts). The prefix makes the coupling visible at call sites; this assertion
 * makes it enforced: a non-Postgres platform fails loudly at first call instead
 * of producing silently-wrong SQL.
 */
trait PostgresPlatformAssertionTrait
{
    private ?bool $postgresAsserted = null;

    private function db(): Connection
    {
        return $this->getEntityManager()->getConnection();
    }

    private function assertPostgres(): void
    {
        if ($this->postgresAsserted === true) {
            return;
        }
        if (!$this->db()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            throw new \LogicException(sprintf(
                '%s::pg*() methods require PostgreSQL; current platform is %s.',
                static::class,
                $this->db()->getDatabasePlatform()::class
            ));
        }
        $this->postgresAsserted = true;
    }
}
