<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Class ProviderInterface
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
interface ProviderInterface
{
    /**
     * Get items
     *
     * @param FilterInterface $filter
     * @return FilterItemInterface[]
     */
    public function getItems($filter);

    /**
     * Retrieve array with items statistics data
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getStatisticsData($filter);
}
