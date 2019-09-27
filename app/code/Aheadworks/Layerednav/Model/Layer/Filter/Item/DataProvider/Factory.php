<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create data provider
     *
     * @param string $type
     * @param DataBuilderInterface $dataBuilder
     * @return DataProviderInterface
     * @throws \Exception
     */
    public function create($type, $dataBuilder)
    {
        $provider = $this->objectManager->create(
            $type,
            [
                'itemDataBuilder' => $dataBuilder
            ]
        );

        if (!$provider instanceof DataProviderInterface) {
            throw new \Exception(sprintf('Type must implement %s interface', DataProviderInterface::class));
        }

        return $provider;
    }
}
