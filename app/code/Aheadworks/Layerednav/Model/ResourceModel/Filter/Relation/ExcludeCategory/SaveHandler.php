<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\ExcludeCategory;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\ExcludeCategory
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
        $tableName = $this->resourceConnection->getTableName('aw_layerednav_filter_exclude_category');
        $connection->delete($tableName, ['filter_id = ?' => $entityId]);

        if ($entity->getCategoryMode() == FilterInterface::CATEGORY_MODE_EXCLUDE) {
            $excludeCategoryIdsToInsert = [];
            $excludeCategoryIds = $entity->getExcludeCategoryIds();
            if (is_array($excludeCategoryIds)) {
                foreach ($excludeCategoryIds as $categoryId) {
                    $excludeCategoryIdsToInsert[] = [
                        'filter_id' => $entityId,
                        'category_id' => $categoryId,
                    ];
                }
                if ($excludeCategoryIdsToInsert) {
                    $connection->insertMultiple($tableName, $excludeCategoryIdsToInsert);
                }
            }
        }

        return $entity;
    }
}
