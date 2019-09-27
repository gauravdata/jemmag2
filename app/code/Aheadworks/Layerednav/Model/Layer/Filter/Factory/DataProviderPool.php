<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Factory;

/**
 * Class DataProviderPool
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Factory
 */
class DataProviderPool
{
    /**
     * @var DataProviderInterface[]
     */
    private $providers;

    /**
     * @param DataProviderInterface[] $providers
     */
    public function __construct(
        array $providers = []
    ) {
        $this->providers = $providers;
    }

    /**
     * Get data provider
     *
     * @param string $type
     * @return DataProviderInterface|mixed
     * @throws \Exception
     */
    public function getDataProvider($type)
    {
        if (!isset($this->providers[$type])) {
            throw new \Exception(sprintf('Unknown filter type: %s requested', $type));
        }
        $provider = $this->providers[$type];
        if (!$provider instanceof DataProviderInterface) {
            throw new \Exception(
                sprintf('Factory data provider must implement %s interface', DataProviderInterface::class)
            );
        }

        return $provider;
    }
}
