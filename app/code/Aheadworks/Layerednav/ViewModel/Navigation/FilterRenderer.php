<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface as LayerFilterInterface;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Magento\Framework\View\LayoutInterface;
use Aheadworks\Layerednav\Block\Filter\Renderer as FilterRendererBlock;

/**
 * Class FilterRenderer
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation
 */
class FilterRenderer implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @param Config $config
     * @param FilterChecker $filterChecker
     */
    public function __construct(
        Config $config,
        FilterChecker $filterChecker
    ) {
        $this->config = $config;
        $this->filterChecker = $filterChecker;
    }

    /**
     * Retrieve render type for the filter
     *
     * @param LayerFilterInterface $filter
     * @return string
     */
    public function getRenderType($filter)
    {
        $renderType = 'default';

        if ($this->filterChecker->isCategory($filter)
            && $this->filterChecker->isSinglePathStyleAppliedForCategoryFilter($filter)
        ) {
            $renderType = 'category_single_path_style';
        } elseif ($this->filterChecker->isSwatchAttribute($filter)) {
            $renderType = 'swatch';
        } elseif ($this->filterChecker->isPrice($filter)
            && $this->config->isManualFromToPriceFilterEnabled()
        ) {
            $renderType = 'price_manual';
        }

        return $renderType;
    }

    /**
     * Retrieve from layout renderer block for specific filter render type
     *
     * @param LayoutInterface $layout
     * @param string $currentBlockName
     * @param string $renderType
     * @return FilterRendererBlock|null;
     */
    public function getRendererBlock($layout, $currentBlockName, $renderType)
    {
        $rendererBlock = null;
        $child = $layout->getChildBlock($currentBlockName, $renderType);
        if ($child && $child instanceof FilterRendererBlock) {
            $rendererBlock = $child;
        }
        return $rendererBlock;
    }
}
