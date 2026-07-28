<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum;

/**
 * Draft metadata scoring type, as sent by Sleeper in `draft.metadata.scoring_type`.
 *
 * Sleeper abbreviates consistently: `std`, `ppr`, `half_ppr`, and the dynasty
 * variants prefix those same tokens. DYNASTY_STANDARD was originally written as
 * `dynasty_standard`, which Sleeper never sends — so the case was unreachable and
 * every dynasty-standard league died on ScoringTypeEnum::from() with
 * "dynasty_std is not a valid backing value", taking the whole league sync with it.
 *
 * This list is NOT authoritative. It is what Sleeper has been observed to send;
 * Sleeper adds values without notice and publishes no schema. That is why the
 * converter now uses tryFrom() and the entity property is nullable: an unknown
 * scoring type must degrade to null, never abort a sync. Add cases here as new
 * values are observed, but do not rely on this being complete.
 */
enum ScoringTypeEnum: string {
    case STANDARD = 'std';
    case PPR = 'ppr';
    case HALF_PPR = 'half_ppr';
    case TWO_QB = '2qb';
    case IDP = 'idp';
    case IDP_1QB = 'idp_1qb';
    case DYNASTY_STANDARD = 'dynasty_std';
    case DYNASTY_PPR = 'dynasty_ppr';
    case DYNASTY_HALF_PPR = 'dynasty_half_ppr';
    case DYNASTY_TWO_QB = 'dynasty_2qb';
    case DYNASTY_IDP = 'dynasty_idp';
}
