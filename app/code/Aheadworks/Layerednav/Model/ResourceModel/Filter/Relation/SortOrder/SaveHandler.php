<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\SortOrder;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\SortOrder
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
        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(FilterInterface::class)->getEntityConnectionName()
        );
        $tableName = $this->resourceConnection->getTableName('aw_layerednav_filter_sort_order');
        $connection->delete($tableName, ['filter_id = ?' => $entityId]);

        $sortOrdersToInsert = [];
        $sortOrders = $entity->getSortOrders();
        if (is_array($sortOrders)) {
            /** @var StoreValueInterface $sortOrder */
            foreach ($entity->getSortOrders() as $sortOrder) {
                $sortOrdersToInsert[] = [
                    'filter_id' => $entityId,
                    'store_id' => $sortOrder->getStoreId(),
                    'value' => $sortOrder->getValue()
                ];
            }
            if ($sortOrdersToInsert) {
                $connection->insertMultiple($tableName, $sortOrdersToInsert);
            }
        }

        return $entity;
    }
}
