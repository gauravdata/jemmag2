<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;

/**
 * Interface FilterManagementInterface
 * @package Aheadworks\Layerednav\Api
 * @api
 */
interface FilterManagementInterface
{
    /**
     * Create filter from an attribute
     *
     * @param ProductAttributeInterface $attribute
     * @return bool
     */
    public function createFilter($attribute);

    /**
     * Check if synchronization needed
     *
     * @param FilterInterface $filter
     * @param ProductAttributeInterface $attribute
     * @return bool
     */
    public function isSyncNeeded($filter, $attribute);

    /**
     * Synchronize filter by id
     *
     * @param int $filterId
     * @return bool
     */
    public function synchronizeFilterById($filterId);

    /**
     * Synchronize filter with attribute
     *
     * @param FilterInterface $filter
     * @param ProductAttributeInterface $attribute
     * @return bool
     */
    public function synchronizeFilter($filter, $attribute);

    /**
     * Synchronize product attribute by filter id
     *
     * @param int $filterId
     * @param bool|false $ignoreFilterType
     * @return bool
     */
    public function synchronizeAttribute($filterId, $ignoreFilterType = false);

    /**
     * Synchronize custom filters
     * @return void
     */
    public function synchronizeCustomFilters();

    /**
     * Synchronize attribute filters
     * @return void
     */
    public function synchronizeAttributeFilters();

    /**
     * Get attribute filter type
     *
     * @param ProductAttributeInterface $attribute
     * @return string
     */
    public function getAttributeFilterType($attribute);
}
