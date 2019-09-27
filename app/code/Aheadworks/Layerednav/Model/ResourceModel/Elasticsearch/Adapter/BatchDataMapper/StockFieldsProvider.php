<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper;

use Aheadworks\Layerednav\Model\Layer\Filter\Custom;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Class StockFieldsProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper
 */
class StockFieldsProvider implements AdditionalFieldsProviderInterface
{
    /**
     * Field name
     */
    const FIELD_NAME = 'aw_stock';

    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @param StockRegistryInterface $stockRegistry
     */
    public function __construct(
        StockRegistryInterface $stockRegistry
    ) {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields(array $productIds, $storeId)
    {
        $fields = [];
        foreach ($productIds as $productId) {
            $fields[$productId] = [
                self::FIELD_NAME => $this->isInStock($productId, $storeId)
                    ? FilterInterface::CUSTOM_FILTER_VALUE_YES
                    : FilterInterface::CUSTOM_FILTER_VALUE_NO
            ];
        }

        return $fields;
    }

    /**
     * Check if product is in stock
     *
     * @param int $productId
     * @param int $storeId
     * @return bool
     */
    private function isInStock($productId, $storeId)
    {
        /** @var StockItemInterface $stockItem */
        $stockItem = $this->stockRegistry->getStockItem($productId, $storeId);

        return $stockItem->getIsInStock();
    }
}
