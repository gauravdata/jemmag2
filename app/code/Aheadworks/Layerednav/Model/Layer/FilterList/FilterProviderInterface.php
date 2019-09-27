<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\FilterList;

use Aheadworks\Layerednav\Api\Data\FilterInterface;

/**
 * Interface FilterProviderInterface
 * @package Aheadworks\Layerednav\Model\Layer\FilterList
 */
interface FilterProviderInterface
{
    /**
     * Get filter data objects
     *
     * @return FilterInterface[]
     */
    public function getFilterDataObjects();
}
