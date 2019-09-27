<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Pool as ItemDataProviderPool;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ProviderFactory
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class ProviderFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ItemDataProviderPool
     */
    private $itemDataProviderPool;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ItemDataProviderPool $itemDataProviderPool
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ItemDataProviderPool $itemDataProviderPool
    ) {
        $this->objectManager = $objectManager;
        $this->itemDataProviderPool = $itemDataProviderPool;
    }

    /**
     * Create items provider
     *
     * @param string $type
     * @param string $sortOrder
     * @return ProviderInterface
     * @throws \Exception
     */
    public function create($type, $sortOrder)
    {
        /** @var DataProviderInterface $itemsDataProvider */
        $itemsDataProvider = $this->itemDataProviderPool->getDataProvider($type, $sortOrder);
        $data = [
            'dataProvider' => $itemsDataProvider
        ];

        return $this->objectManager->create(ProviderInterface::class, $data);
    }
}
