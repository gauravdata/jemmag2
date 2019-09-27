<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Applier\Price;

use Aheadworks\Layerednav\Model\Config;

/**
 * Class ConditionResolver
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Applier\Price
 */
class ConditionResolver
{
    /**
     * Price delta for filter
     */
    const PRICE_DELTA = 0.001;

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
     * Get from-o condition
     *
     * @param int|string $from
     * @param int|string $to
     * @return array
     */
    public function getFromToCondition($from, $to)
    {
        if (!empty($to)
            && $from != $to
            && !$this->config->isManualFromToPriceFilterEnabled()
        ) {
            $to = $to - self::PRICE_DELTA;
        }

        return ['from' => $from, 'to' => $to];
    }
}
