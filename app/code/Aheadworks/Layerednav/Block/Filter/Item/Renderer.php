<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\Filter\Item;

use Magento\Framework\View\Element\Template;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as LayerFilterItemInterface;

/**
 * Class Renderer
 *
 * @package Aheadworks\Layerednav\Block\Filter\Item
 */
class Renderer extends Template
{
    /**
     * Render specified layer filter item
     *
     * @param LayerFilterItemInterface $filterItem
     * @return string
     */
    public function render(LayerFilterItemInterface $filterItem)
    {
        $this->assign('filterItem', $filterItem);
        $html = $this->_toHtml();
        $this->assign('filterItem', null);
        return $html;
    }
}
