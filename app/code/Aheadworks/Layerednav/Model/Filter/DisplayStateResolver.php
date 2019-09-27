<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Config;

/**
 * Class DisplayStateResolver
 * @package Aheadworks\Layerednav\Model\Filter
 */
class DisplayStateResolver
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get storefront display state
     *
     * @param FilterInterface $filter
     * @return int
     */
    public function getStorefrontDisplayState(FilterInterface $filter)
    {
        $storeFrontDisplayState = $filter->getStorefrontDisplayState();
        if (!$storeFrontDisplayState) {
            $storeFrontDisplayState = $this->config->getFilterDisplayState();
        }

        return $storeFrontDisplayState;
    }
}
