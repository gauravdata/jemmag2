<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches;

/**
 * Interface DataResolverInterface
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches
 */
interface DataResolverInterface
{
    /**
     * Retrieve CSS classes array
     *
     * @return array
     */
    public function getClasses();

    /**
     * Retrieve tooltip value
     *
     * @param array $optionData
     * @return string
     */
    public function getTooltipValue($optionData);

    /**
     * Retrieve tooltip thumb
     *
     * @param array $optionData
     * @return string
     */
    public function getTooltipThumb($optionData);

    /**
     * Retrieve custom CSS style string
     *
     * @param array $optionData
     * @return string
     */
    public function getCustomStyle($optionData);
}
