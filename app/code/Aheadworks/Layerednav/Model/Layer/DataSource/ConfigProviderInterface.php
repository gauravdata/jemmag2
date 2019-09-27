<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\DataSource;

/**
 * Interface ConfigProviderInterface
 * @package Aheadworks\Layerednav\Model\Layer\DataSource
 */
interface ConfigProviderInterface
{
    /**
     * Get data source config
     *
     * @return array
     */
    public function getConfig();
}
