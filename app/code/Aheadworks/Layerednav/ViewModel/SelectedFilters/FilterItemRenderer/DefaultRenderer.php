<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\SelectedFilters\FilterItemRenderer;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\ViewModel\SelectedFilters\Resolver as SelectedFiltersResolver;

/**
 * Class DefaultRenderer
 *
 * @package Aheadworks\Layerednav\ViewModel\SelectedFilters\FilterItemRenderer
 */
class DefaultRenderer implements ArgumentInterface
{
    /**
     * @var SelectedFiltersResolver
     */
    private $selectedFiltersResolver;

    /**
     * @param SelectedFiltersResolver $selectedFiltersResolver
     */
    public function __construct(
        SelectedFiltersResolver $selectedFiltersResolver
    ) {
        $this->selectedFiltersResolver = $selectedFiltersResolver;
    }

    /**
     * Get filter item html Id
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getItemHtmlId(FilterItemInterface $filterItem)
    {
        return 'aw-filter-option-' . $filterItem->getFilter()->getCode() . '-' . $filterItem->getValue();
    }

    /**
     * Get filter item label
     *
     * @param FilterItemInterface $item
     * @return string
     */
    public function getLabel(FilterItemInterface $item)
    {
        return $this->selectedFiltersResolver->getLabel($item);
    }
}
