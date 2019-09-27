<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface FilterSearchResultsInterface
 * @package Aheadworks\Layerednav\Api\Data
 * @api
 */
interface FilterSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get filters
     *
     * @return FilterInterface[]
     */
    public function getItems();

    /**
     * Set filters
     *
     * @param FilterInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
