<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url;

/**
 * Interface ConverterInterface
 * @package Aheadworks\Layerednav\Model\Url
 */
interface ConverterInterface
{
    /**
     * Converts filter params
     *
     * @param array $params
     * @return array
     */
    public function convertFilterParams($params);
}
