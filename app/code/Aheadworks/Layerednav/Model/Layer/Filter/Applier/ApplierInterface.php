<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Interface ApplierInterface
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier
 */
interface ApplierInterface
{
    /**
     * Apply filter
     *
     * @param RequestInterface $request
     * @param FilterInterface $filter
     * @return $this
     */
    public function apply($request, $filter);
}
