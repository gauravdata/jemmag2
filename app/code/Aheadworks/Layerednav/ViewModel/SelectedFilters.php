<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\ViewModel\SelectedFilters\Resolver as SelectedFiltersResolver;

/**
 * Class SelectedFilters
 *
 * @package Aheadworks\Layerednav\ViewModel
 */
class SelectedFilters implements ArgumentInterface
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
     * Get active filter items
     *
     * @return FilterItemInterface[]
     */
    public function getActiveFilterItems()
    {
        return $this->selectedFiltersResolver->getFilterItems();
    }

    /**
     * Check if need to render block
     *
     * @return bool
     */
    public function isNeedToRender()
    {
        return !empty($this->getActiveFilterItems());
    }
}
