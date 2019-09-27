<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\State\Item;

use Aheadworks\Layerednav\Model\Layer\State\Item as StateItem;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Aheadworks\Layerednav\Model\Layer\FilterInterface as LayerFilterInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\Layerednav\Model\Layer\State\Item
 */
class Resolver
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
     * Retrieve first state item, related to the specific filter
     *
     * @param LayerFilterInterface $filter
     * @return StateItem|null
     */
    public function getItemByFilter($filter)
    {
        $stateItem = null;
        $activeFilterItems = $this->layerState->getItems();
        if (!empty($activeFilterItems)) {
            foreach ($activeFilterItems as $activeItem) {
                if ($activeItem->getFilterItem()->getFilter()->getCode() == $filter->getCode()) {
                    $stateItem = $activeItem;
                    break;
                }
            }
        }
        return $stateItem;
    }
}
