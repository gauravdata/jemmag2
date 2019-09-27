<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\SelectedFilters;

use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\Layerednav\ViewModel\SelectedFilters
 */
class Resolver
{
    /**
     * @var LayerState
     */
    private $layerState;

    /**
     * @var FilterItemInterface[]
     */
    private $activeFilterItems = [];

    /**
     * Associative array, where keys are labels of filter items, and values are total counts of labels usage
     *
     * @var int[]
     */
    private $labelsCounterMap = [];

    /**
     * @param LayerState $layerState
     */
    public function __construct(
        LayerState $layerState
    ) {
        $this->layerState = $layerState;
    }

    /**
     * Get active filter items
     *
     * @return FilterItemInterface[]
     */
    public function getFilterItems()
    {
        if (empty($this->activeFilterItems)) {
            $this->fillActiveFilterItemsArray();
        }
        return $this->activeFilterItems;
    }

    /**
     * Retrieve full label of filter item to display inside selected filters block
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    public function getLabel($filterItem)
    {
        $shortLabel = $this->getItemLabel($filterItem);
        if ($this->isNeedToUseFilterName($shortLabel)) {
            $fullLabel = ((string)__($filterItem->getFilter()->getTitle())) . ': ' . $shortLabel;
        } else {
            $fullLabel = $shortLabel;
        }

        return $fullLabel;
    }

    /**
     * Fill active filter items array with data from layer state
     */
    private function fillActiveFilterItemsArray()
    {
        $activeItems = $this->layerState->getItems();
        if (!empty($activeItems)) {
            foreach ($activeItems as $activeItem) {
                $filterItem = $activeItem->getFilterItem();
                $this->activeFilterItems[] = $filterItem;
            }
        }
    }

    /**
     * Retrieve label to display for specific filter item
     *
     * @param FilterItemInterface $filterItem
     * @return string
     */
    private function getItemLabel($filterItem)
    {
        return (string)__($filterItem->getLabel());
    }

    /**
     * Check if label is duplicated and it's necessary to add filter name to label
     *
     * @param string $label
     * @return bool
     */
    private function isNeedToUseFilterName($label)
    {
        if (empty($this->labelsCounterMap)) {
            $this->fillLabelsCounterMap();
        }
        if (isset($this->labelsCounterMap[$label])) {
            return $this->labelsCounterMap[$label] > 1;
        } else {
            return false;
        }
    }

    /**
     * Fill labels counter map with labels usage count data
     */
    private function fillLabelsCounterMap()
    {
        foreach ($this->getFilterItems() as $filterItem) {
            $label = $this->getItemLabel($filterItem);
            if (isset($this->labelsCounterMap[$label])) {
                $this->labelsCounterMap[$label]++;
            } else {
                $this->labelsCounterMap[$label] = 1;
            }
        }
    }
}
