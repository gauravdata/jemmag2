<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;

/**
 * Interface ParserInterface
 * @package Aheadworks\Layerednav\App\Request
 */
interface ParserInterface
{
    /**
     * Parse request
     *
     * @param RequestInterface|Http $request
     * @return bool
     */
    public function parse(RequestInterface $request);
}
