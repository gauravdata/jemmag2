<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Factory as DataProviderFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderPool;

/**
 * Class Pool
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider
 */
class Pool
{
    /**
     * @var DataBuilderPool
     */
    private $dataBuilderPool;

    /**
     * @var DataProviderFactory
     */
    private $dataProviderFactory;

    /**
     * @var string[]
     */
    private $providers;

    /**
     * @param DataBuilderPool $dataBuilderPool
     * @param DataProviderFactory $dataProviderFactory
     * @param string[] $providers
     */
    public function __construct(
        DataBuilderPool $dataBuilderPool,
        DataProviderFactory $dataProviderFactory,
        array $providers = []
    ) {
        $this->dataBuilderPool = $dataBuilderPool;
        $this->dataProviderFactory = $dataProviderFactory;
        $this->providers = $providers;
    }

    /**
     * Get data provider
     *
     * @param string $type
     * @param string $sortOrder
     * @return DataProviderInterface
     * @throws \Exception
     */
    public function getDataProvider($type, $sortOrder)
    {
        if (!isset($this->providers[$type])) {
            throw new \Exception(sprintf('Unknown data provider type: %s requested', $type));
        }

        $dataProvider = $this->dataProviderFactory->create(
            $this->providers[$type],
            $this->dataBuilderPool->getDataBuilder($sortOrder)
        );

        return $dataProvider;
    }
}
