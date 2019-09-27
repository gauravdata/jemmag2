<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block;

use Magento\Framework\View\Element\Template;
use Aheadworks\Layerednav\Block\Filter\Item\Renderer as FilterItemRenderer;

/**
 * Class SelectedFilters
 *
 * @package Aheadworks\Layerednav\Block
 */
class SelectedFilters extends Template
{
    /**
     * Retrieve filter item renderer block
     *
     * @return FilterItemRenderer|null
     */
    public function getFilterItemRendererBlock()
    {
        $block = $this->getChildBlock('renderer');
        if ($block && $block instanceof FilterItemRenderer) {
            return $block;
        } else {
            return null;
        }
    }
}
