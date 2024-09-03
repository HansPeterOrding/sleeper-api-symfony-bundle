<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperTransaction as SleeperTransactionDto;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\TransactionStatusEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\Enum\TransactionTypeEnum;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperTransaction as SleeperTransactionEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperMatchupRepository;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperTransactionRepository;

class SleeperTransactionConverter implements ConverterInterface {
    public function __construct(
        private readonly SleeperTransactionRepository            $sleeperTransactionRepository,
        private readonly SleeperTransactionWaiverBudgetConverter $sleeperTransactionWaiverBudgetConverter,
        private readonly SleeperTransactionSettingsConverter     $sleeperTransactionSettingsConverter,
        private readonly SleeperTransactionMetadataConverter     $sleeperTransactionMetadataConverter
    )
    {
    }

    public function toEntity(SleeperTransactionDto $sleeperTransactionDto): SleeperTransactionEntity
    {
        $sleeperTransactionEntity = $this->sleeperTransactionRepository->findByDtoOrCreateEntity($sleeperTransactionDto);

        $sleeperTransactionEntity->setType(TransactionTypeEnum::from($sleeperTransactionDto->getType()));
        $sleeperTransactionEntity->setTransactionId($sleeperTransactionDto->getTransactionId());
        $sleeperTransactionEntity->setStatusUpdated($sleeperTransactionDto->getStatusUpdated());
        $sleeperTransactionEntity->setStatus(TransactionStatusEnum::from($sleeperTransactionDto->getStatus()));
        $sleeperTransactionEntity->setRosterIds($sleeperTransactionDto->getRosterIds());
        $sleeperTransactionEntity->setLeg($sleeperTransactionDto->getLeg());
        $sleeperTransactionEntity->setDrops($sleeperTransactionDto->getDrops());
        $sleeperTransactionEntity->setCreator($sleeperTransactionDto->getCreator());
        $sleeperTransactionEntity->setCreated($sleeperTransactionDto->getCreated());
        $sleeperTransactionEntity->setConsenterIds($sleeperTransactionDto->getConsenterIds());
        $sleeperTransactionEntity->setAdds($sleeperTransactionDto->getAdds());
        $sleeperTransactionEntity->setDraftPicks($sleeperTransactionDto->getDraftPicks());

        $sleeperTransactionWaiverBudgetEntity = $this->sleeperTransactionWaiverBudgetConverter->toEntity(
            $sleeperTransactionDto->getWaiverBudget(),
            $sleeperTransactionEntity->getWaiverBudget()
        );
        $sleeperTransactionEntity->setWaiverBudget($sleeperTransactionWaiverBudgetEntity);

        $sleeperTransactionSettingsEntity = $this->sleeperTransactionSettingsConverter->toEntity(
            $sleeperTransactionDto->getSettings(),
            $sleeperTransactionEntity->getSettings()
        );
        $sleeperTransactionEntity->setSettings($sleeperTransactionSettingsEntity);

        if ($sleeperTransactionDto->getMetadata()) {
            $sleeperTransactionMetadataEntity = $this->sleeperTransactionMetadataConverter->toEntity(
                $sleeperTransactionDto->getMetadata(),
                $sleeperTransactionEntity->getMetadata()
            );
            $sleeperTransactionEntity->setMetadata($sleeperTransactionMetadataEntity);
        }

        return $sleeperTransactionEntity;
    }
}
