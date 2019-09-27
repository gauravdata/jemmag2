<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Interface DataProviderInterface
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
interface DataProviderInterface
{
    /**
     * Get items data
     *
     * @param FilterInterface $filter
     * @return array [['label' => '', 'value' => '', 'count' => '', 'imageData'], ...]
     */
    public function getItemsData($filter);

    /**
     * Retrieve items statistics data
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getStatisticsData($filter);
}
