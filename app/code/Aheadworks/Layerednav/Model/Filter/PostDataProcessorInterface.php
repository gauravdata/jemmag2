<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter;

/**
 * Interface PostDataProcessorInterface
 * @package Aheadworks\Layerednav\Model\Filter
 */
interface PostDataProcessorInterface
{
    /**
     * Prepare entity data for save
     *
     * @param array $data
     * @return array
     */
    public function process($data);
}
