<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner;

/**
 * Class CleanerInterface
 * @package Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner
 */
interface CleanerInterface
{
    /**
     * Clean not needed search container data
     *
     * @param array $data
     * @param string $filter
     * @return array
     */
    public function perform($data, $filter);
}
