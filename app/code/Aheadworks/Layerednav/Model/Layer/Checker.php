<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\FilterInterface as LayerFilterInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;

/**
 * Class Checker
 *
 * @package Aheadworks\Layerednav\Model\Layer
 */
class Checker
{
    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var LayerState
     */
    private $layerState;

    /**
     * @param FilterChecker $filterChecker
     * @param LayerState $layerState
     */
    public function __construct(
        FilterChecker $filterChecker,
        LayerState $layerState
    ) {
        $this->filterChecker = $filterChecker;
        $this->layerState = $layerState;
    }

    /**
     * Check if current layer has applied filters
     *
     * @return bool
     */
    public function hasActiveFilters()
    {
        $activeFilterItems = $this->layerState->getItems();
        return count($activeFilterItems) > 0;
    }

    /**
     * Check if there is a few values for the same filter selected
     *
     * @return bool
     */
    public function hasActiveFilterWithFewValues()
    {
        $hasActiveFilterWithFewValues = false;
        $activeFiltersCodes = [];
        $stateItems = $this->layerState->getItems();
        if (!empty($stateItems)) {
            foreach ($stateItems as $stateItem) {
                $filter = $stateItem->getFilterItem()->getFilter();
                if (in_array($filter->getCode(), $activeFiltersCodes)) {
                    $hasActiveFilterWithFewValues = true;
                    break;
                } else {
                    $activeFiltersCodes[] = $filter->getCode();
                }
            }
        }
        return $hasActiveFilterWithFewValues;
    }

    /**
     * Check if layered navigation available for current layer
     *
     * @param LayerFilterInterface[] $filters
     * @return bool
     */
    public function isNavigationAvailable($filters)
    {
        return $this->isAtLeastOneFilterVisible($filters) || $this->hasActiveFilters();
    }

    /**
     * Check if at least one filter from array is visible
     *
     * @param LayerFilterInterface[] $filters
     * @return bool
     */
    protected function isAtLeastOneFilterVisible($filters)
    {
        $isAtLeastOneFilterHasItems = false;
        foreach ($filters as $filter) {
            if ($this->filterChecker->isNeedToDisplay($filter)) {
                $isAtLeastOneFilterHasItems = true;
                break;
            }
        }

        return $isAtLeastOneFilterHasItems;
    }
}
