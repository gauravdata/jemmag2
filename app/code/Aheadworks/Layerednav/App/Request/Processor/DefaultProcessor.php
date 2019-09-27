<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request\Processor;

use Aheadworks\Layerednav\App\Request\ProcessorInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class DefaultProcessor
 * @package Aheadworks\Layerednav\App\Request\Processor
 */
class DefaultProcessor implements ProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(RequestInterface $request)
    {
        return $request;
    }
}
