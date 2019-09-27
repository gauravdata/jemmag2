<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Mode;

use Aheadworks\Layerednav\Api\Data\FilterExtensionInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Mode
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @param FilterInterface $entity
     * @param array $arguments
     * @return FilterInterface
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $tableName = $this->resourceConnection->getTableName(FilterResource::FILTER_MODE_TABLE_NAME);
        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(FilterInterface::class)->getEntityConnectionName()
        );

        $this->removeOldModesData($entityId, $connection, $tableName);

        $modesDataToSave = $this->getModesDataToSave($entity);
        if (!empty($modesDataToSave)) {
            $connection->insertMultiple($tableName, $modesDataToSave);
        }

        return $entity;
    }

    /**
     * Remove old modes data
     *
     * @param int $entityId
     * @param AdapterInterface $connection
     * @param string $tableName
     * @throws \Exception
     */
    private function removeOldModesData($entityId, $connection, $tableName)
    {
        $connection->delete($tableName, ['filter_id = ?' => $entityId]);
    }

    /**
     * Get modes data to save
     *
     * @param FilterInterface $entity
     * @return array
     */
    private function getModesDataToSave($entity)
    {
        $entityId = (int)$entity->getId();
        $modesToInsert = [];
        /** @var FilterExtensionInterface $extensionAttributes */
        $extensionAttributes = $entity->getExtensionAttributes();
        if ($extensionAttributes->getFilterMode()) {
            $filterModeEntity = $extensionAttributes->getFilterMode();
            $filterModes = $filterModeEntity->getFilterModes();
            if (is_array($filterModes)) {
                /** @var StoreValueInterface $displayState */
                foreach ($filterModes as $filterMode) {
                    if ($filterMode instanceof StoreValueInterface) {
                        $modesToInsert[] = [
                            'filter_id' => $entityId,
                            'store_id' => $filterMode->getStoreId(),
                            'value' => $filterMode->getValue()
                        ];
                    } else {
                        $modesToInsert[] = [
                            'filter_id' => $entityId,
                            'store_id' => $filterMode['store_id'],
                            'value' => $filterMode['value']
                        ];
                    }
                }
            }
        }

        return $modesToInsert;
    }
}
