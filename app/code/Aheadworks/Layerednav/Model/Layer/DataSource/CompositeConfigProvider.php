<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\DataSource;

/**
 * Class CompositeConfigProvider
 * @package Aheadworks\Layerednav\Model\Layer\DataSource
 */
class CompositeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var ConfigProviderInterface[]
     */
    private $providers;

    /**
     * @param array $providers
     */
    public function __construct(array $providers)
    {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        foreach ($this->providers as $provider) {
            $config = array_merge($config, $provider->getConfig());
        }
        return $config;
    }
}
