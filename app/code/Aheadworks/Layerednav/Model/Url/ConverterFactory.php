<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ConverterFactory
 * @package Aheadworks\Layerednav\Model\Url
 */
class ConverterFactory
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
     * Create converter instance
     *
     * @param string $className
     * @return ConverterInterface
     */
    public function create($className)
    {
        return $this->objectManager->create($className);
    }
}
