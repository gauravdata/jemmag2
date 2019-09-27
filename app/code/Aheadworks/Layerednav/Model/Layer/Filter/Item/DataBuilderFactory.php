<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class DataBuilderFactory
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class DataBuilderFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create builder instance
     *
     * @param string $className
     * @return DataBuilderInterface
     */
    public function create($className)
    {
        return $this->objectManager->create($className);
    }
}
