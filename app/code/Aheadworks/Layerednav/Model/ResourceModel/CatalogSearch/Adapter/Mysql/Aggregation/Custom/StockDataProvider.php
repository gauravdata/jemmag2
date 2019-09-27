<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\Custom;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\AggregationProviderInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\Dimension;

/**
 * Class StockDataProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\Custom
 * @codeCoverageIgnore
 */
class StockDataProvider implements AggregationProviderInterface
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Get stock data set
     *
     * @param Dimension[] $dimensions
     * @param Table $entityIdsTable
     * @return Select
     * @throws \Zend_Db_Exception
     */
    public function getDataSet(array $dimensions, Table $entityIdsTable)
    {
        /** @var Select $select */
        $select = $this->getSelect();

        $select
            ->from(
                ['main_table' => $entityIdsTable->getName()],
                []
            )
            ->joinInner(
                ['stock' => $this->getStockTable()],
                'main_table.entity_id = stock.product_id AND stock.stock_id = 1',
                ['value' => 'stock_status']
            );

        $select = $this->getSelect()
            ->from(['main_table' => $select]);

        return $select;
    }

    /**
     * Get select
     *
     * @return Select
     */
    private function getSelect()
    {
        return $this->resource->getConnection()->select();
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
