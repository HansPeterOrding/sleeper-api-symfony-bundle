<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Service;

use HansPeterOrding\SleeperApiSymfonyBundle\Exception\ImportConfigurationException;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperDraftImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperDraftPickImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperLeagueImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperMatchupImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperPlayoffMatchupImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperRosterImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperTradedPickImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperTransactionImporter;
use HansPeterOrding\SleeperApiSymfonyBundle\Importer\SleeperUserImporter;

class ImportService
{
    public const IMPORT_ENTITY_LEAGUE = 'import_entity_league';
    public const IMPORT_ENTITY_LEAGUE_DRAFT = 'import_entity_league_draft';
    public const IMPORT_ENTITY_LEAGUE_USERS = 'import_entity_league_users';
    public const IMPORT_ENTITY_LEAGUE_ROSTERS = 'import_entity_league_rsters';
    public const IMPORT_ENTITY_LEAGUE_DRAFT_PICKS = 'import_entity_league_draft_picks';
    public const IMPORT_ENTITY_LEAGUE_MATCHUPS = 'import_entity_league_matchups';
    public const IMPORT_ENTITY_LEAGUE_PLAYOFF_MATCHUPS = 'import_entity_league_playoff_matchups';
    public const IMPORT_ENTITY_LEAGUE_TRADED_PICKS = 'import_entity_league_traded_picks';
    public const IMPORT_ENTITY_LEAGUE_TRANSACTIONS = 'import_entity_league_transactions';

    public const COMPLETE_IMPORT_LEAGUE_ENTITIES = [
        self::IMPORT_ENTITY_LEAGUE,
        self::IMPORT_ENTITY_LEAGUE_USERS,
        self::IMPORT_ENTITY_LEAGUE_ROSTERS,
        self::IMPORT_ENTITY_LEAGUE_DRAFT,
        self::IMPORT_ENTITY_LEAGUE_DRAFT_PICKS,
        self::IMPORT_ENTITY_LEAGUE_MATCHUPS,
        self::IMPORT_ENTITY_LEAGUE_PLAYOFF_MATCHUPS,
        self::IMPORT_ENTITY_LEAGUE_TRADED_PICKS,
        self::IMPORT_ENTITY_LEAGUE_TRANSACTIONS,
    ];

    public function __construct(
        private readonly SleeperLeagueImporter $sleeperLeagueImporter,
        private readonly SleeperUserImporter $sleeperUserImporter,
        private readonly SleeperRosterImporter $sleeperRosterImporter,
        private readonly SleeperDraftImporter $sleeperDraftImporter,
        private readonly SleeperDraftPickImporter $sleeperDraftPickImporter,
        private readonly SleeperMatchupImporter $sleeperMatchupImporter,
        private readonly SleeperPlayoffMatchupImporter $sleeperPlayoffMatchupImporter,
        private readonly SleeperTradedPickImporter $sleeperTradedPickImporter,
        private readonly SleeperTransactionImporter $sleeperTransactionImporter
    )
    {
    }

    public function importSleeperLeague(string $sleeperLeagueId, array $importEntities = ImportService::COMPLETE_IMPORT_LEAGUE_ENTITIES)
    {
        $sleeperUsers = [];
        $sleeperRosters = [];
        $sleeperDraft = null;

        $sleeperLeague = $this->sleeperLeagueImporter->import($sleeperLeagueId);

        if(in_array(self::IMPORT_ENTITY_LEAGUE_USERS, $importEntities)) {
            $sleeperUsers = $this->sleeperUserImporter->importLeagueUsers($sleeperLeagueId);
        }

        if(in_array(self::IMPORT_ENTITY_LEAGUE_ROSTERS, $importEntities)) {
            $sleeperRosters = $this->sleeperRosterImporter->importLeagueRosters($sleeperLeagueId, $sleeperUsers, $sleeperLeague);
        }

        if(in_array(self::IMPORT_ENTITY_LEAGUE_DRAFT, $importEntities)) {
            $sleeperDraft = $this->sleeperDraftImporter->import($sleeperLeague->getDraftId(), $sleeperLeague);
        }

        if(in_array(self::IMPORT_ENTITY_LEAGUE_DRAFT_PICKS, $importEntities)) {
            if(!in_array(self::IMPORT_ENTITY_LEAGUE_DRAFT, $importEntities)) {
                throw new ImportConfigurationException('Invalid configuration for import: Draft picks can only imported when draft is also imported. Please add ImportService::IMPORT_ENTITY_LEAGUE_DRAFT to defined $importEntities.');
            }
            $sleeperDraftPicks = $this->sleeperDraftPickImporter->importDraftPicks($sleeperDraft);
        }

        if(in_array(self::IMPORT_ENTITY_LEAGUE_MATCHUPS, $importEntities)) {
            $this->sleeperMatchupImporter->importMatchups($sleeperLeague, $sleeperRosters);
        }

        if(in_array(self::IMPORT_ENTITY_LEAGUE_PLAYOFF_MATCHUPS, $importEntities)) {
            $sleeperPlayoffMatchups = $this->sleeperPlayoffMatchupImporter->importPlayoffMatchups($sleeperLeague);
        }

        if(in_array(self::IMPORT_ENTITY_LEAGUE_TRADED_PICKS, $importEntities)) {
            $sleeperTradedPicks = $this->sleeperTradedPickImporter->importTradedPicks($sleeperLeague, $sleeperDraft);
        }

        if(in_array(self::IMPORT_ENTITY_LEAGUE_TRANSACTIONS, $importEntities)) {
            $sleeperTransactions = $this->sleeperTransactionImporter->importTransactions($sleeperLeague);
        }
    }
}
