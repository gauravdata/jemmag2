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
 * Class ReadHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\ExcludeCategory
 */
class ReadHandler implements ExtensionInterface
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
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection
    ) {
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
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(FilterInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName('aw_layerednav_filter_exclude_category'))
                ->where('filter_id = :id');
            $excludeCategoriesData = $connection->fetchAll(
                $select,
                ['id' => $entityId]
            );

            $excludeCategoryIds = [];
            foreach ($excludeCategoriesData as $excludeCategoryData) {
                $excludeCategoryIds[] = (int)$excludeCategoryData['category_id'];
            }
            $entity
                ->setExcludeCategoryIds($excludeCategoryIds);
        }
        return $entity;
    }
}
