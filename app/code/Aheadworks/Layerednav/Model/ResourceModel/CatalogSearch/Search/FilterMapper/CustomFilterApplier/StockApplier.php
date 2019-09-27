<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\FilterApplierInterface;
use Aheadworks\Layerednav\Model\Store\Resolver as StoreResolver;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class StockApplier
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier
 * @codeCoverageIgnore
 */
class StockApplier implements FilterApplierInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @param ResourceConnection $resource
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        ResourceConnection $resource,
        StoreResolver $storeResolver
    ) {
        $this->resource = $resource;
        $this->storeResolver = $storeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Context $context, FilterInterface $filter, Select $select)
    {
        $storeId = $context->getStoreId();
        $websiteId = $this->storeResolver->getWebsiteIdByStoreId($storeId);

        $connection = $select->getConnection();
        $joinCondition = 'search_index.entity_id = aw_stock_filter.product_id' .
            $connection->quoteInto(' AND aw_stock_filter.website_id IN (?, 0)', $websiteId);

        $select->joinLeft(
            ['aw_stock_filter' => $this->getStockTable()],
            $joinCondition,
            []
        );
        $select->where('aw_stock_filter.stock_status = 1');

        return true;
    }

    /**
     * Get stock table
     *
     * @return string
     */
    private function getStockTable()
    {
        return $this->resource->getTableName('cataloginventory_stock_status');
    }
}
