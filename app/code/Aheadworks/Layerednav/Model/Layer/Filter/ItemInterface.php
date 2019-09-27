<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Interface ItemInterface
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 */
interface ItemInterface
{
    /**
     * Get filter
     *
     * @return FilterInterface
     */
    public function getFilter();

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Get value
     *
     * @return string
     */
    public function getValue();

    /**
     * Get count
     *
     * @return int
     */
    public function getCount();

    /**
     * Get image data
     *
     * @return array
     */
    public function getImageData();
}
