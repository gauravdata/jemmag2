<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Config;

/**
 * Class ModeResolver
 * @package Aheadworks\Layerednav\Model\Filter
 */
class ModeResolver
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $singleSelectFilters;

    /**
     * @var array
     */
    private $multiSelectFilters;

    /**
     * @param Config $config
     * @param array $singleSelectFilters
     * @param array $multiSelectFilters
     */
    public function __construct(
        Config $config,
        array $singleSelectFilters = [],
        array $multiSelectFilters = []
    ) {
        $this->config = $config;
        $this->singleSelectFilters = $singleSelectFilters;
        $this->multiSelectFilters = $multiSelectFilters;
    }

    /**
     * Get storefront filter mode
     *
     * @param FilterInterface $filter
     * @return string
     */
    public function getStorefrontFilterMode(FilterInterface $filter)
    {
        $storefrontFilterMode = ModeInterface::MODE_MULTI_SELECT;
        if (in_array($filter->getType(), $this->singleSelectFilters)) {
            $storefrontFilterMode = ModeInterface::MODE_SINGLE_SELECT;
        } elseif (!in_array($filter->getType(), $this->multiSelectFilters)) {
            /** @var ModeInterface|null $filterMode */
            $filterMode = $filter->getExtensionAttributes()->getFilterMode();
            $storefrontFilterMode = $filterMode->getStorefrontFilterMode();
            if (!$storefrontFilterMode) {
                $storefrontFilterMode = $this->config->getFilterMode();
            }
        }

        return $storefrontFilterMode;
    }
}
