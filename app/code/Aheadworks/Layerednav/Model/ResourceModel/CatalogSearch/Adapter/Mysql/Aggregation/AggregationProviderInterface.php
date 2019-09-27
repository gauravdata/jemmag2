<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\Dimension;

/**
 * Class AggregationProviderInterface
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation
 */
interface AggregationProviderInterface
{
    /**
     * Get aggregation data set
     *
     * @param Dimension[] $dimensions
     * @param Table $entityIdsTable
     * @return Select
     */
    public function getDataSet(array $dimensions, Table $entityIdsTable);
}
