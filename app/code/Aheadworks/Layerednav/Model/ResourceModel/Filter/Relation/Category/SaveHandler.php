<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Category;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class SaveHandler
 * @package Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Category
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
     * @param FilterInterface|Filter $entity
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
        $tableName = $this->resourceConnection->getTableName('aw_layerednav_filter_category');
        $connection->delete($tableName, ['filter_id = ?' => $entityId]);

        /** @var FilterCategoryInterface|null $filterCategory */
        $filterCategory = $entity->getCategoryFilterData();
        if ($filterCategory) {
            $filterCategoryItemsToInsert = [];
            $listStyles = $filterCategory->getListStyles();
            if (is_array($listStyles)) {
                /** @var StoreValueInterface $listStyles */
                foreach ($listStyles as $listStyle) {
                    $filterCategoryItemsToInsert[] = [
                        'filter_id' => $entityId,
                        'store_id' => $listStyle->getStoreId(),
                        'param_name' => FilterCategoryInterface::LIST_PARAM_NAME,
                        'value' => $listStyle->getValue()
                    ];
                }
                if ($filterCategoryItemsToInsert) {
                    $connection->insertMultiple($tableName, $filterCategoryItemsToInsert);
                }
            }
        }

        return $entity;
    }
}
