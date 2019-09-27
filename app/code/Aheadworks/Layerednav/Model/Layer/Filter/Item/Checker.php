<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as LayerFilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;

/**
 * Class Checker
 *
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class Checker
{
    /**
     * @var LayerState
     */
    private $layerState;

    /**
     * @param LayerState $layerState
     */
    public function __construct(
        LayerState $layerState
    ) {
        $this->layerState = $layerState;
    }

    /**
     * Check if filter item is active
     *
     * @param LayerFilterItemInterface $filterItem
     * @return bool
     */
    public function isActive($filterItem)
    {
        $activeFilterItems = $this->layerState->getItems();
        if (!empty($activeFilterItems)) {
            foreach ($activeFilterItems as $activeItem) {
                if ($activeItem->getFilterItem()->getFilter()->getCode() == $filterItem->getFilter()->getCode()) {
                    if ($filterItem->getValue() == $activeItem->getFilterItem()->getValue()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}
