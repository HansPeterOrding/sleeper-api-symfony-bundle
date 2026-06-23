<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Service;

class SleeperImportService
{
    public const string IMPORT_ENTITY_USERS_AND_ROSTERS  = 'import_entity_users_and_rosters';
    public const string IMPORT_ENTITY_DRAFT              = 'import_entity_draft';
    public const string IMPORT_ENTITY_DRAFT_PICKS        = 'import_entity_draft_picks';
    public const string IMPORT_ENTITY_TRADED_PICKS       = 'import_entity_traded_picks';
    public const string IMPORT_ENTITY_MATCHUPS           = 'import_entity_matchups';
    public const string IMPORT_ENTITY_PLAYOFF_MATCHUPS   = 'import_entity_playoff_matchups';
    public const string IMPORT_ENTITY_TRANSACTIONS       = 'import_entity_transactions';

    public static function getDefaultImportEntities(): array
    {
        return [
            self::IMPORT_ENTITY_USERS_AND_ROSTERS => true,
            self::IMPORT_ENTITY_DRAFT             => true,
            self::IMPORT_ENTITY_DRAFT_PICKS       => true,
            self::IMPORT_ENTITY_TRADED_PICKS      => true,
            self::IMPORT_ENTITY_MATCHUPS          => true,
            self::IMPORT_ENTITY_PLAYOFF_MATCHUPS  => true,
            self::IMPORT_ENTITY_TRANSACTIONS      => true,
        ];
    }

    public function shouldImport(?array $importEntities, string $key): bool
    {
        if ($importEntities === null) {
            return true;
        }

        return isset($importEntities[$key]) && $importEntities[$key] !== false;
    }
}
