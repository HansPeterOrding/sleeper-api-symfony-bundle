<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Importer;

use Doctrine\ORM\EntityManagerInterface;
use HansPeterOrding\SleeperApiClient\ApiClient\Endpoints\AbstractEndpoint;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\ConverterInterface;
use HansPeterOrding\SleeperApiSymfonyBundle\Converter\SleeperPlayoffMatchupConverter;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperDraftPick;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperLeague;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperPlayoffMatchup;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperMatchupRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperRosterRepository;

/**
 * @property SleeperPlayoffMatchupConverter $converter
 */
class SleeperPlayoffMatchupImporter extends AbstractImporter {
    public function __construct(
        private readonly SleeperRosterRepository  $sleeperRosterRepository,
        private readonly SleeperMatchupRepository $sleeperMatchupRepository,
        ConverterInterface                        $converter,
        EntityManagerInterface                    $entityManager
    )
    {
        parent::__construct($converter, $entityManager);
    }

    /**
     * @return SleeperDraftPick[]
     */
    public function importPlayoffMatchups(SleeperLeague $sleeperLeague): array
    {
        foreach ([AbstractEndpoint::BRANCH_WINNERS, AbstractEndpoint::BRANCH_LOSERS] as $branch) {
            $sleeperPlayoffMatchups = $this->sleeperApiClient->league()->listPlayoffMatchups($sleeperLeague->getLeagueId(), $branch);

            $entities = [];

            foreach ($sleeperPlayoffMatchups as $sleeperPlayoffMatchup) {
                $entity = $this->converter->toEntity(
                    $sleeperLeague->getLeagueId(),
                    $branch,
                    $sleeperPlayoffMatchup
                );

                $entity->setLeague($sleeperLeague);

                if ($entity->getT1()) {
                    $this->applyTeam(1, $entity, $sleeperLeague);
                }

                if ($entity->getT2()) {
                    $this->applyTeam(2, $entity, $sleeperLeague);
                }

                $this->entityManager->persist($entity);
                $this->entityManager->flush();

                $entities[] = $entity;
            }
        }

        return $entities;
    }

    private function applyTeam(int $teamNr, SleeperPlayoffMatchup $entity, SleeperLeague $sleeperLeague): SleeperPlayoffMatchup
    {
        $roster = $this->sleeperRosterRepository->findOneBy([
            'leagueId' => $sleeperLeague->getLeagueId(),
            'rosterId' => ($teamNr === 1 ? $entity->getT1() : $entity->getT2())
        ]);

        if ($teamNr === 1) {
            $entity->setRosterTeam1($roster);
        } else {
            $entity->setRosterTeam2($roster);
        }

        if ($entity->getM()) {
            $matchupTeam = $this->sleeperMatchupRepository->findOneBy([
                'leagueId' => $sleeperLeague->getLeagueId(),
                'rosterId' => ($teamNr === 1 ? $entity->getT1() : $entity->getT2()),
                'week' => $entity->getR() + $sleeperLeague->getSettings()->getPlayoffWeekStart() - 1
            ]);

            if ($teamNr === 1) {
                $entity->setMatchupTeam1($matchupTeam);
            } else {
                $entity->setMatchupTeam2($matchupTeam);
            }
        }

        return $entity;
    }
}
