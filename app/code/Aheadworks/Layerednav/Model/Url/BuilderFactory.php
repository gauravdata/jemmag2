<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class BuilderFactory
 * @package Aheadworks\Layerednav\Model\Url
 */
class BuilderFactory
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
     * @return BuilderInterface
     */
    public function create($className)
    {
        return $this->objectManager->create($className);
    }
}
