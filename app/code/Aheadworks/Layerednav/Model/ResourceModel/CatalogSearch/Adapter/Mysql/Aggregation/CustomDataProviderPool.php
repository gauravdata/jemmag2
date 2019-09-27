<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation;

/**
 * Class CustomDataProviderPool
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation
 */
class CustomDataProviderPool
{
    /**
     * @var AggregationProviderInterface[]
     */
    private $providers;

    /**
     * @param AggregationProviderInterface[] $providers
     */
    public function __construct(
        array $providers = []
    ) {
        $this->providers = $providers;
    }

    /**
     * Get aggregation provider
     *
     * @param string $field
     * @return AggregationProviderInterface|null
     */
    public function getAggregationProvider($field)
    {
        if (isset($this->providers[$field])) {
            return $this->providers[$field];
        }

        return null;
    }
}
