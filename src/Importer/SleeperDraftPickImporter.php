<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\Exception\NotFoundException;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperDraftPickConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperRosterConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\FantasyPositionEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\InjuryStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\PlayerStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\SportEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraft;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperRoster;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperPlayerRepository;

/**
 * @property SleeperDraftPickConverter $converter
 */
class SleeperDraftPickImporter extends AbstractImporter {
    public function __construct(
        private readonly SleeperPlayerRepository $sleeperPlayerRepository,
        private readonly Connection $db,
        ConverterInterface                       $converter,
        EntityManagerInterface                   $entityManager
    )
    {
        parent::__construct($converter, $entityManager);
    }

    /**
     * @return SleeperDraftPick[]
     */
    public function importDraftPicks(SleeperDraft $sleeperDraft): void
    {
        $sleeperDraftPicks = $this->sleeperApiClient->draft()->listPicks($sleeperDraft->getDraftId());

        $internalDraftId = $sleeperDraft->getId();
        if (!$internalDraftId) {
            // Draft muss bereits persistiert sein, sonst können wir keine FK setzen.
            throw new \RuntimeException('SleeperDraft must be persisted before importing draft picks.');
        }

        $draftId  = $sleeperDraft->getDraftId();
        $leagueId = $this->fetchLeagueIdForDraft($draftId);

        // IDs sammeln (für FK-Mappings)
        $externalPlayerIds = [];
        $externalUserIds   = [];
        $externalRosterIds = [];

        foreach ($sleeperDraftPicks as $dto) {
            $pid = trim((string)$dto->getPlayerId());
            if ($pid !== '') {
                $externalPlayerIds[] = $pid;
            }

            $uid = trim((string)$dto->getPickedBy());
            if ($uid !== '') {
                $externalUserIds[] = $uid;
            }

            $rid = $dto->getRosterId();
            if ($rid !== null) {
                $externalRosterIds[] = (int)$rid;
            }
        }

        $playerIdMap = $this->fetchPlayerIdMap($externalPlayerIds); // [external player_id => internal id]
        $userIdMap   = $this->fetchUserIdMap($externalUserIds);     // [external user_id   => internal id]
        if($leagueId) {
            $rosterIdMap = $this->fetchRosterMapByLeagueId($leagueId);  // [roster_id          => internal id]
        }

        $rows     = [];

        foreach ($sleeperDraftPicks as $dto) {
            // FK-Auflösung via bereits synchronisierte Tabellen
            $pid              = $dto->getPlayerId();
            $internalPlayerId = ($pid && isset($playerIdMap[$pid])) ? $playerIdMap[$pid] : null;

            $pickedBy        = $dto->getPickedBy();
            $internalUserId  = ($pickedBy && isset($userIdMap[$pickedBy])) ? $userIdMap[$pickedBy] : null;

            $rid             = $dto->getRosterId();
            $internalRosterId = ($rid !== null && isset($rosterIdMap[(int)$rid])) ? $rosterIdMap[(int)$rid] : null;

            $metadataStatus = null;
            if($dto->getMetadata()->getStatus() !== "") {
                $metadataStatus = PlayerStatusEnum::from($dto->getMetadata()->getStatus());
            }
            $rows[] = [
                'internal_draft_id'  => $internalDraftId,
                'internal_player_id' => $internalPlayerId,
                'internal_roster_id' => $internalRosterId,
                'internal_user_id'   => $internalUserId,

                'round'      => $dto->getRound(),
                'roster_id'  => $dto->getRosterId(),
                'player_id'  => $dto->getPlayerId(),
                'picked_by'  => $dto->getPickedBy(),
                'pick_no'    => $dto->getPickNo(),
                'is_keeper'  => $this->normalizeNullableBool($dto->getIsKeeper()) ?? false,
                'draft_slot' => $dto->getDraftSlot(),
                'draft_id'   => $dto->getDraftId(),

                // Embedded metadata_* Spalten
                'metadata_years_exp'     => $dto->getMetadata()->getYearsExp(),
                'metadata_team'          => $dto->getMetadata()->getTeam(),
                'metadata_status'        => $metadataStatus?->value,
                'metadata_sport'         => SportEnum::from($dto->getMetadata()->getSport())->value,
                'metadata_position'      => FantasyPositionEnum::from($dto->getMetadata()->getPosition())->value,
                'metadata_player_id'     => $dto->getMetadata()->getPlayerId(),
                'metadata_number'        => $dto->getMetadata()->getNumber(),
                'metadata_news_updated'  => $dto->getMetadata()->getNewsUpdated(),
                'metadata_last_name'     => $dto->getMetadata()->getLastName(),
                'metadata_injury_status' => InjuryStatusEnum::from($dto->getMetadata()->getInjuryStatus())->value,
                'metadata_first_name'    => $dto->getMetadata()->getFirstName(),
                'metadata_amount'        => $dto->getMetadata()->getAmount(),
            ];
        }

        // Schreiben (chunked) in einer Transaktion
        $this->db->beginTransaction();
        try {
            foreach (array_chunk($rows, 250) as $chunk) {
                $this->bulkUpsertDraftPicks($chunk);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function fetchLeagueIdForDraft(string $draftId): ?string
    {
        $leagueId = $this->db->fetchOne(
            'SELECT league_id FROM public.sasb_sleeper_draft WHERE draft_id = ?',
            [$draftId]
        );

        if (!$leagueId) {
            return null;
        }

        return (string)$leagueId;
    }

    /** @return array<int,int> [roster_id => internal_roster_id] */
    private function fetchRosterMapByLeagueId(string $leagueId): array
    {
        $rows = $this->db->fetchAllAssociative(
            'SELECT roster_id, id FROM public.sasb_sleeper_roster WHERE league_id = ?',
            [$leagueId]
        );

        $map = [];
        foreach ($rows as $r) {
            $map[(int)$r['roster_id']] = (int)$r['id'];
        }
        return $map;
    }

    /** @param string[] $externalPids @return array<string,int> [external player_id => internal id] */
    private function fetchPlayerIdMap(array $externalPids): array
    {
        $externalPids = array_values(array_filter(array_map(static fn($v) => trim((string)$v), $externalPids)));
        if ($externalPids === []) {
            return [];
        }
        $externalPids = array_values(array_unique($externalPids));

        $map = [];
        foreach (array_chunk($externalPids, 1000) as $chunk) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT player_id, id FROM public.sasb_sleeper_player WHERE player_id IN (?)',
                [$chunk],
                [ArrayParameterType::STRING]
            );

            foreach ($rows as $r) {
                $map[(string)$r['player_id']] = (int)$r['id'];
            }
        }

        return $map;
    }

    /** @param string[] $externalUserIds @return array<string,int> [external user_id => internal id] */
    private function fetchUserIdMap(array $externalUserIds): array
    {
        $externalUserIds = array_values(array_filter(array_map(static fn($v) => trim((string)$v), $externalUserIds)));
        if ($externalUserIds === []) {
            return [];
        }
        $externalUserIds = array_values(array_unique($externalUserIds));

        $map = [];
        foreach (array_chunk($externalUserIds, 1000) as $chunk) {
            $rows = $this->db->fetchAllAssociative(
                'SELECT user_id, id FROM public.sasb_sleeper_user WHERE user_id IN (?)',
                [$chunk],
                [ArrayParameterType::STRING]
            );

            foreach ($rows as $r) {
                $map[(string)$r['user_id']] = (int)$r['id'];
            }
        }

        return $map;
    }

    /** @param array<int,array<string,mixed>> $chunk */
    private function bulkUpsertDraftPicks(array $chunk): void
    {
        if ($chunk === []) {
            return;
        }

        $tuple = '(' . implode(',', [
                '?', // internal_draft_id
                '?', // internal_player_id
                '?', // internal_roster_id
                '?', // internal_user_id
                '?', // round
                '?', // roster_id
                '?', // player_id
                '?', // picked_by
                '?', // pick_no
                'COALESCE(NULLIF(?::text, \'\')::boolean, false)', // is_keeper
                '?', // draft_slot
                '?', // draft_id

                // metadata_*
                '?', // metadata_years_exp
                '?', // metadata_team
                '?', // metadata_status
                '?', // metadata_sport
                '?', // metadata_position
                '?', // metadata_player_id
                '?', // metadata_number
                '?', // metadata_news_updated
                '?', // metadata_last_name
                '?', // metadata_injury_status
                '?', // metadata_first_name
                '?', // metadata_amount
            ]) . ')';

        $valuesSql = implode(',', array_fill(0, count($chunk), $tuple));

        $sql = <<<SQL
INSERT INTO public.sasb_sleeper_draft_pick
  (internal_draft_id, internal_player_id, internal_roster_id, internal_user_id,
   round, roster_id, player_id, picked_by, pick_no, is_keeper, draft_slot, draft_id,
   metadata_years_exp, metadata_team, metadata_status, metadata_sport, metadata_position,
   metadata_player_id, metadata_number, metadata_news_updated, metadata_last_name,
   metadata_injury_status, metadata_first_name, metadata_amount)
VALUES $valuesSql
ON CONFLICT (draft_id, pick_no)
DO UPDATE SET
  internal_draft_id      = EXCLUDED.internal_draft_id,
  internal_player_id     = EXCLUDED.internal_player_id,
  internal_roster_id     = EXCLUDED.internal_roster_id,
  internal_user_id       = EXCLUDED.internal_user_id,
  round                  = EXCLUDED.round,
  roster_id              = EXCLUDED.roster_id,
  player_id              = EXCLUDED.player_id,
  picked_by              = EXCLUDED.picked_by,
  is_keeper              = EXCLUDED.is_keeper,
  draft_slot             = EXCLUDED.draft_slot,
  metadata_years_exp     = EXCLUDED.metadata_years_exp,
  metadata_team          = EXCLUDED.metadata_team,
  metadata_status        = EXCLUDED.metadata_status,
  metadata_sport         = EXCLUDED.metadata_sport,
  metadata_position      = EXCLUDED.metadata_position,
  metadata_player_id     = EXCLUDED.metadata_player_id,
  metadata_number        = EXCLUDED.metadata_number,
  metadata_news_updated  = EXCLUDED.metadata_news_updated,
  metadata_last_name     = EXCLUDED.metadata_last_name,
  metadata_injury_status = EXCLUDED.metadata_injury_status,
  metadata_first_name    = EXCLUDED.metadata_first_name,
  metadata_amount        = EXCLUDED.metadata_amount
SQL;

        $params = [];
        foreach ($chunk as $r) {
            $params[] = $r['internal_draft_id'];
            $params[] = $r['internal_player_id'];
            $params[] = $r['internal_roster_id'];
            $params[] = $r['internal_user_id'];
            $params[] = $r['round'];
            $params[] = $r['roster_id'];
            $params[] = $r['player_id'];
            $params[] = $r['picked_by'];
            $params[] = $r['pick_no'];
            $params[] = $r['is_keeper'];
            $params[] = $r['draft_slot'];
            $params[] = $r['draft_id'];

            $params[] = $r['metadata_years_exp'];
            $params[] = $r['metadata_team'];
            $params[] = $r['metadata_status'];
            $params[] = $r['metadata_sport'];
            $params[] = $r['metadata_position'];
            $params[] = $r['metadata_player_id'];
            $params[] = $r['metadata_number'];
            $params[] = $r['metadata_news_updated'];
            $params[] = $r['metadata_last_name'];
            $params[] = $r['metadata_injury_status'];
            $params[] = $r['metadata_first_name'];
            $params[] = $r['metadata_amount'];
        }

        $this->db->executeStatement($sql, $params);
    }

    private function normalizeNullableBool(mixed $value): ?bool
    {
        if ($value === null) {
            return null;
        }

        if ($value === true || $value === 1 || $value === '1' || $value === 'true' || $value === 't') {
            return true;
        }

        if ($value === false || $value === 0 || $value === '0' || $value === 'false' || $value === 'f') {
            return false;
        }

        // Wichtig: fängt '' und alles Unbekannte ab
        return null;
    }
}
