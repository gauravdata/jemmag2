<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Aheadworks\Layerednav\Model\Url\BuilderPool;

/**
 * Class UrlManager
 * @package Aheadworks\Layerednav\Model
 */
class UrlManager
{
    /**
     * @var BuilderPool
     */
    private $builderPool;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param BuilderPool $builderPool
     * @param Config $config
     */
    public function __construct(
        BuilderPool $builderPool,
        Config $config
    ) {
        $this->builderPool = $builderPool;
        $this->config = $config;
    }

    /**
     * Get current url of given type
     *
     * @param string $fromType
     * @param string $toType
     * @return string
     * @throws \Exception
     */
    public function getCurrentUrl($fromType, $toType)
    {
        return $this->builderPool->getUrlBuilder($toType)
            ->getCurrentUrl($fromType);
    }

    /**
     * Get current canonical url
     *
     * @return string
     * @throws \Exception
     */
    public function getCurrentCanonicalUrl()
    {
        return $this->builderPool
            ->getUrlBuilder($this->config->getSeoFriendlyUrlOption())
            ->getCurrentCanonicalUrl();
    }
}
