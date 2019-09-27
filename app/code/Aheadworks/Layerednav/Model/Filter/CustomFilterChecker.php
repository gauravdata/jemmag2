<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Config;

/**
 * Class CustomFilterChecker
 * @package Aheadworks\Layerednav\Model\Filter
 */
class CustomFilterChecker
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $customFilters;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Check if the specified filter type is a custom filter type
     *
     * @param string $type
     * @return bool
     */
    public function isCustom($type)
    {
        return in_array($type, FilterInterface::CUSTOM_FILTER_TYPES);
    }

    /**
     * Check if custom filter type is available
     *
     * @param string $type
     * @return bool
     */
    public function isAvailable($type)
    {
        if (!$this->customFilters) {
            $this->customFilters[] = FilterInterface::CATEGORY_FILTER;
            if ($this->config->isNewFilterEnabled()) {
                $this->customFilters[] = FilterInterface::NEW_FILTER;
            }
            if ($this->config->isInStockFilterEnabled()) {
                $this->customFilters[] = FilterInterface::STOCK_FILTER;
            }
            if ($this->config->isOnSaleFilterEnabled()) {
                $this->customFilters[] = FilterInterface::SALES_FILTER;
            }
        }

        return in_array($type, $this->customFilters);
    }
}
