<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\SelectedFilters;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Magento\Framework\View\LayoutInterface;
use Aheadworks\Layerednav\Block\Filter\Item\Renderer as FilterItemRendererBlock;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;

/**
 * Class FilterItemRenderer
 *
 * @package Aheadworks\Layerednav\ViewModel\SelectedFilters
 */
class FilterItemRenderer implements ArgumentInterface
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
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getRenderType($filterItem)
    {
        $renderType = 'default';

        $filter = $filterItem->getFilter();

        if ($this->filterChecker->isPrice($filter)
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
     * @return FilterItemRendererBlock|null;
     */
    public function getRendererBlock($layout, $currentBlockName, $renderType)
    {
        $rendererBlock = null;
        $child = $layout->getChildBlock($currentBlockName, $renderType);
        if ($child && $child instanceof FilterItemRendererBlock) {
            $rendererBlock = $child;
        }
        return $rendererBlock;
    }
}
