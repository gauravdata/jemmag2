<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemFactory as FilterItemFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Class Provider
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class Provider implements ProviderInterface
{
    /**
     * @var FilterItemFactory
     */
    private $filterItemFactory;

    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @param FilterItemFactory $filterItemFactory
     * @param DataProviderInterface $dataProvider
     */
    public function __construct(
        FilterItemFactory $filterItemFactory,
        DataProviderInterface $dataProvider
    ) {
        $this->filterItemFactory = $filterItemFactory;
        $this->dataProvider = $dataProvider;
    }

    /**
     * Get items
     *
     * @param FilterInterface $filter
     * @return FilterItemInterface[]
     */
    public function getItems($filter)
    {
        $itemsData = $this->dataProvider->getItemsData($filter);
        $items = [];
        foreach ($itemsData as $itemData) {
            $items[] = $this->filterItemFactory->create(
                [
                    'filter'    => $filter,
                    'label'     => $itemData['label'],
                    'value'     => $itemData['value'],
                    'count'     => $itemData['count'],
                    'imageData' => isset($itemData['imageData']) ? $itemData['imageData'] : [],
                ]
            );
        }

        return $items;
    }

    /**
     * Retrieve array with items statistics data
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getStatisticsData($filter)
    {
        return $this->dataProvider->getStatisticsData($filter);
    }
}
