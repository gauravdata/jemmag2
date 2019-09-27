<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Source\Filter\PriceSlider;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class BehaviourMode
 *
 * @package Aheadworks\Layerednav\Model\Source\Filter\PriceSlider
 */
class BehaviourMode implements OptionSourceInterface
{
    /**#@+
     * Option values
     */
    const CONTINUOUS = 1;
    const DISCRETE = 2;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CONTINUOUS,
                'label' => __('Continuous')
            ],
            [
                'value' => self::DISCRETE,
                'label' => __('Discrete')
            ],
        ];
    }
}
