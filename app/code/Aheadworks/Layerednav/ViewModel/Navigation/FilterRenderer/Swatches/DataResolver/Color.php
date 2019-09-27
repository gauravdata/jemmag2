<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver;

use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolverInterface;

/**
 * Class Color
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver
 */
class Color implements DataResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return [
            'color' => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTooltipValue($optionData)
    {
        return isset($optionData['value']) ? $optionData['value'] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTooltipThumb($optionData)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomStyle($optionData)
    {
        $color = isset($optionData['value']) ? $optionData['value'] : '';
        return "background: " . $color . " no-repeat center; background-size: initial;";
    }
}
