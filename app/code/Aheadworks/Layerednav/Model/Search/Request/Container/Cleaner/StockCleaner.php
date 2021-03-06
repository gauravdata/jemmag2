<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner;

use Aheadworks\Layerednav\Model\Config;

/**
 * Class StockCleaner
 * @package Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner
 */
class StockCleaner extends AbstractCleaner
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function perform($data, $filter)
    {
        if (!$this->config->isInStockFilterEnabled()) {
            $data = $this->cleanData($data, $filter);
        }

        return $data;
    }
}
