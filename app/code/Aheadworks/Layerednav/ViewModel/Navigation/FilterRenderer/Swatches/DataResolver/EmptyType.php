<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver;

use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolverInterface;

/**
 * Class EmptyType
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver
 */
class EmptyType implements DataResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getTooltipValue($optionData)
    {
        return '';
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
        return '';
    }
}
