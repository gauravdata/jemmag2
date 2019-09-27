<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\State;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;

/**
 * Class DefaultLayerState
 * @package Aheadworks\Layerednav\Model\Layer\State
 */
class DefaultLayerState
{
    /**
     * @var DefaultFilterFactory
     */
    private $defaultFilterFactory;

    /**
     * @var ItemFactory
     */
    private $defaultItemFactory;

    /**
     * @param DefaultFilterFactory $defaultFilterFactory
     * @param ItemFactory $defaultItemFactory
     */
    public function __construct(
        DefaultFilterFactory $defaultFilterFactory,
        ItemFactory $defaultItemFactory
    ) {
        $this->defaultFilterFactory = $defaultFilterFactory;
        $this->defaultItemFactory = $defaultItemFactory;
    }

    /**
     * @param ItemInterface $filterItem
     */
    public function addFilter($filterItem)
    {
        /** @var FilterInterface $filter */
        $filter = $filterItem->getFilter();
        /** @var Layer $layer */
        $layer = $filter->getLayer();

        /** @var DefaultFilter $defaultFilter */
        $defaultFilter = $this->defaultFilterFactory->create(
            [
                'layer' => $layer
            ]
        );
        $defaultFilter
            ->setRequestVar($filter->getCode());

        /** @var Item */
        $defaultFilterItem = $this->defaultItemFactory->create();
        $defaultFilterItem
            ->setFilter($defaultFilter)
            ->setLabel($filterItem->getLabel())
            ->setValue($filterItem->getValue())
            ->setCount($filterItem->getCount());

        $layer
            ->getState()
            ->addFilter($defaultFilterItem);
    }
}
