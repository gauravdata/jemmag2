<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Block\Filter;

use Magento\Framework\View\Element\Template;
use Aheadworks\Layerednav\Model\Layer\FilterInterface as LayerFilterInterface;

/**
 * Class Renderer
 *
 * @package Aheadworks\Layerednav\Block\Filter
 */
class Renderer extends Template
{
    /**
     * Render specified layer filter
     *
     * @param LayerFilterInterface $filter
     * @return string
     */
    public function render(LayerFilterInterface $filter)
    {
        $this->assign('filter', $filter);
        $html = $this->_toHtml();
        $this->assign('filter', null);
        return $html;
    }
}
