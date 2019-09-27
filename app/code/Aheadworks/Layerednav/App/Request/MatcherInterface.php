<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;

/**
 * Interface MatcherInterface
 * @package Aheadworks\Layerednav\App\Request
 */
interface MatcherInterface
{
    /**
     * Match action by request
     *
     * @param RequestInterface|Http $request
     * @return bool
     */
    public function match(RequestInterface $request);

    /**
     * Get matcher type
     *
     * @return string
     */
    public function getType();
}
