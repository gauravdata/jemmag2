<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\Context;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * Interface FilterApplierInterface
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper
 */
interface FilterApplierInterface
{
    /**
     * Apply filter
     *
     * @param Context $context
     * @param FilterInterface $filter
     * @param Select $select
     * @return bool
     */
    public function apply(
        Context $context,
        FilterInterface $filter,
        Select $select
    );
}
