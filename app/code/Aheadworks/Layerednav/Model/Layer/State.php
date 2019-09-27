<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\State\DefaultLayerState;
use Aheadworks\Layerednav\Model\Layer\State\Item as StateItem;
use Aheadworks\Layerednav\Model\Layer\State\ItemFactory as StateItemFactory;

/**
 * Class State
 * @package Aheadworks\Layerednav\Model\Layer
 */
class State
{
    /**
     * @var StateItemFactory
     */
    private $stateItemFactory;

    /**
     * @var DefaultLayerState
     */
    private $defaultLayerState;

    /**
     * @var StateItem[]
     */
    private $items = [];

    /**
     * @param StateItemFactory $stateItemFactory
     * @param DefaultLayerState $defaultLayerState
     */
    public function __construct(
        StateItemFactory $stateItemFactory,
        DefaultLayerState $defaultLayerState
    ) {
        $this->stateItemFactory = $stateItemFactory;
        $this->defaultLayerState = $defaultLayerState;
    }

    /**
     * Add filter
     *
     * @param FilterItemInterface $item
     * @param string $field
     * @param array $condition
     * @param bool $orOption
     * @return $this
     */
    public function addFilter(FilterItemInterface $item, $field, $condition, $orOption = false)
    {
        /** @var StateItem $stateItem */
        $stateItem = $this->stateItemFactory->create();
        $stateItem
            ->setFilterItem($item)
            ->setFilterField($field)
            ->setFilterCondition($condition)
            ->setFilterOrOption($orOption);

        $this->items[] = $stateItem;

        $this->defaultLayerState->addFilter($item);

        return $this;
    }

    /**
     * Get items
     *
     * @return StateItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Reset items
     *
     * @return $this
     */
    public function resetItems()
    {
        $this->items = [];

        return $this;
    }
}
