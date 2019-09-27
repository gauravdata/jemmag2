<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\State;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;

/**
 * Class Applier
 * @package Aheadworks\Layerednav\Model\Layer\State
 */
class Applier
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
     * Add data to layer filter state
     *
     * @param FilterItemInterface[] $filterItems
     * @param string $field
     * @param array $condition
     * @param bool $orOption
     * @return $this
     */
    public function add($filterItems, $field, $condition, $orOption)
    {
        $appliedToCollection = false;
        foreach ($filterItems as $filterItem) {
            if (!$appliedToCollection) {
                /** @var FilterInterface $filter */
                $filter = $filterItem->getFilter();
                $this->applyToCollection($filter, $field, $condition);
                $appliedToCollection = true;
            }
            $this->layerState->addFilter($filterItem, $field, $condition, $orOption);
        }

        return $this;
    }

    /**
     * Apply filter to collection
     *
     * @param FilterInterface $filter
     * @param $field
     * @param $condition
     * @return void
     */
    private function applyToCollection(FilterInterface $filter, $field, $condition)
    {
        /** @var Collection $productCollection */
        $productCollection = $filter->getLayer()
            ->getProductCollection();
        $productCollection->addFieldToFilter(
            $field,
            $condition
        );
    }
}
