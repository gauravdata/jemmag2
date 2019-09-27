<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url;

/**
 * Class BuilderPool
 * @package Aheadworks\Layerednav\Model\Url
 */
class BuilderPool
{
    /**
     * @var array
     */
    private $urlBuilders = [];

    /**
     * @var BuilderInterface[]
     */
    private $urlBuilderInstances = [];

    /**
     * @var BuilderFactory
     */
    private $builderFactory;

    /**
     * @param BuilderFactory $builderFactory
     * @param array $urlBuilders
     */
    public function __construct(
        BuilderFactory $builderFactory,
        $urlBuilders = []
    ) {
        $this->builderFactory = $builderFactory;
        $this->urlBuilders = $urlBuilders;
    }

    /**
     * Retrieve url builder instance of given type
     *
     * @param string $type
     * @return BuilderInterface
     * @throws \Exception
     */
    public function getUrlBuilder($type)
    {
        if (!isset($this->urlBuilderInstances[$type])) {
            if (!isset($this->urlBuilders[$type])) {
                throw new \Exception(sprintf('Unknown url builder type: %s requested', $type));
            }
            $builderInstance = $this->builderFactory->create($this->urlBuilders[$type]);
            if (!$builderInstance instanceof BuilderInterface) {
                throw new \Exception(
                    sprintf('Url builder instance %s does not implement required interface.', $type)
                );
            }
            $this->urlBuilderInstances[$type] = $builderInstance;
        }
        return $this->urlBuilderInstances[$type];
    }
}
