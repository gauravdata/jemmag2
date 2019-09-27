<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;

/**
 * Interface ProcessorInterface
 * @package Aheadworks\Layerednav\App\Request
 */
interface ProcessorInterface
{
    /**
     * Process request
     *
     * @param RequestInterface|Http $request
     * @return RequestInterface|Http
     */
    public function process(RequestInterface $request);
}
