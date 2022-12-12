<?php

declare(strict_types=1);

namespace HansPeterOrding\SleeperApiSymfonyBundle\DoctrineExtensions;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;

class TablePrefixEventListener
{
    protected array $config;

    public function __construct(
        private readonly string $prefix
    )
    {
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

        $nameSpaces = explode('\\', $classMetadata->getName());
        if(!array_key_exists(0, $nameSpaces) || !array_key_exists(1, $nameSpaces) || $nameSpaces[0] !== 'HansPeterOrding' || $nameSpaces[1] !== 'SleeperApiSymfonyBundle') {
            return;
        }

        if($classMetadata->namespace === 'App\Entity\Contact') {
            return;
        }

        if(!$classMetadata->isInheritanceTypeSingleTable() || $classMetadata->getName() === $classMetadata->rootEntityName) {
            $classMetadata->setPrimaryTable([
                'name' => $this->prefix . $classMetadata->getTableName()
            ]);
        }

        foreach($classMetadata->getAssociationMappings() as $fieldName => $mapping) {
            if($mapping['type'] == ClassMetadata::MANY_TO_MANY && $mapping['isOwningSide']) {
                $mappedTableName = $mapping['joinTable']['name'];
                $classMetadata->associationMappings[$fieldName]['joinTable']['name'] = $this->prefix . $mappedTableName;
            }
        }
    }
}
