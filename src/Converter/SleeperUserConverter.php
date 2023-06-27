<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\Converter;

use HansPeterOrding\SleeperApiClient\Dto\SleeperUser as SleeperUserDto;
use HansPeterOrding\SleeperApiClient\Dto\SleeperUserMetadata;
use HansPeterOrding\SleeperApiSymfonyBundle\Entity\SleeperUser as SleeperUserEntity;
use HansPeterOrding\SleeperApiSymfonyBundle\Repository\SleeperUserRepository;

class SleeperUserConverter
{
    public function __construct(
        private readonly SleeperUserRepository        $sleeperUserRepository,
        private readonly SleeperUserMetadataConverter $sleeperUserMetadataConverter
    ) {
    }

    public function toEntity(SleeperUserDto $sleeperUserDto): SleeperUserEntity
    {
        $sleeperUserEntity = $this->sleeperUserRepository->findByDtoOrCreateEntity($sleeperUserDto);

        $sleeperUserEntity->setVerification($sleeperUserDto->getVerification());
        $sleeperUserEntity->setUsername($sleeperUserDto->getUsername());
        $sleeperUserEntity->setUserId($sleeperUserDto->getUserId());
        $sleeperUserEntity->setToken($sleeperUserDto->getToken());
        $sleeperUserEntity->setSummonerRegion($sleeperUserDto->getSummonerRegion());
        $sleeperUserEntity->setSummonerName($sleeperUserDto->getSummonerName());
        $sleeperUserEntity->setSolicitable($sleeperUserDto->getSolicitable());
        $sleeperUserEntity->setRealName($sleeperUserDto->getRealName());
        $sleeperUserEntity->setPhone($sleeperUserDto->getPhone());
        $sleeperUserEntity->setPending($sleeperUserDto->getPending());
        $sleeperUserEntity->setIsBot($sleeperUserDto->getIsBot());
        $sleeperUserEntity->setEmail($sleeperUserDto->getEmail());
        $sleeperUserEntity->setDisplayName($sleeperUserDto->getDisplayName());
        $sleeperUserEntity->setAvatar($sleeperUserDto->getAvatar());

        $sleeperUserMetadataEntity = $this->sleeperUserMetadataConverter->toEntity(
            $sleeperUserDto->getMetadata(),
            $sleeperUserEntity->getMetadata()
        );
        $sleeperUserEntity->setMetadata($sleeperUserMetadataEntity);

        return $sleeperUserEntity;
    }
}
