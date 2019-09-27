<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\DisplayState;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\DisplayState
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
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(FilterInterface::class)->getEntityConnectionName()
        );
        $tableName = $this->resourceConnection->getTableName('aw_layerednav_filter_display_state');
        $connection->delete($tableName, ['filter_id = ?' => $entityId]);

        $displayStatesToInsert = [];
        $displayStates = $entity->getDisplayStates();
        if (is_array($displayStates)) {
            /** @var StoreValueInterface $displayState */
            foreach ($entity->getDisplayStates() as $displayState) {
                $displayStatesToInsert[] = [
                    'filter_id' => $entityId,
                    'store_id' => $displayState->getStoreId(),
                    'value' => $displayState->getValue()
                ];
            }
            if ($displayStatesToInsert) {
                $connection->insertMultiple($tableName, $displayStatesToInsert);
            }
        }

        return $entity;
    }
}
