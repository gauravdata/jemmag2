<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Layer\Filter\Item as FilterItem;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemFactory as FilterItemFactory;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Class ItemList
 * @package Aheadworks\Layerednav\Model\Layer\Filter
 */
class ItemListBuilder
{
    /**
     * @var FilterItemFactory
     */
    private $filterItemFactory;

    /**
     * @var array
     */
    private $itemsData = [];

    /**
     * @param ItemFactory $filterItemFactory
     */
    public function __construct(
        FilterItemFactory $filterItemFactory
    ) {
        $this->filterItemFactory = $filterItemFactory;
    }

    /**
     * Add filter item data
     *
     * @param FilterInterface $filter
     * @param string $label
     * @param string|int $value
     * @param int $count
     * @return $this
     */
    public function add($filter, $label, $value, $count)
    {
        $this->itemsData[] = [
            'filter' => $filter,
            'label' => $label,
            'value' => $value,
            'count' => $count
        ];

        return $this;
    }

    /**
     * Create items list
     *
     * @return array
     */
    public function create()
    {
        $items = [];
        foreach ($this->itemsData as $itemData) {
            /** @var FilterItem $item */
            $item = $this->filterItemFactory->create(
                [
                    'filter' => $itemData['filter'],
                    'label' => $itemData['label'],
                    'value' => $itemData['value'],
                    'count' => $itemData['count']
                ]
            );
            $items[] = $item;
        }
        $this->itemsData = [];

        return $items;
    }
}
