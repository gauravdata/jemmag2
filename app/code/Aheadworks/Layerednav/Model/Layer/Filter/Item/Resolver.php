<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\State\Item\Resolver as StateItemResolver;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Layer\Filter\Interval\Resolver as FilterIntervalResolver;

/**
 * Class Resolver
 *
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item
 */
class Resolver
{
    /**
     * @var StateItemResolver
     */
    private $stateItemResolver;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var FilterIntervalResolver
     */
    private $filterIntervalResolver;

    /**
     * @param StateItemResolver $stateItemResolver
     * @param FilterChecker $filterChecker
     * @param FilterIntervalResolver $filterIntervalResolver
     */
    public function __construct(
        StateItemResolver $stateItemResolver,
        FilterChecker $filterChecker,
        FilterIntervalResolver $filterIntervalResolver
    ) {
        $this->stateItemResolver = $stateItemResolver;
        $this->filterChecker = $filterChecker;
        $this->filterIntervalResolver = $filterIntervalResolver;
    }

    /**
     * Retrieve active filter item by related filter
     *
     * @param FilterInterface $filter
     * @return ItemInterface|null
     */
    public function getActiveItemByFilter($filter)
    {
        $filterItem = null;
        $stateItem = $this->stateItemResolver->getItemByFilter($filter);
        if ($stateItem) {
            $filterItem = $stateItem->getFilterItem();
        }
        return $filterItem;
    }

    /**
     * Retrieve price-from value from price filter value
     *
     * @param ItemInterface $filterItem
     * @return float
     */
    public function getPriceFromValue($filterItem)
    {
        if ($this->filterChecker->isPrice($filterItem->getFilter())) {
            $interval = $this->filterIntervalResolver->getInterval($filterItem->getValue());
            return $interval ? $interval->getFrom() : 0;
        } else {
            return 0;
        }
    }

    /**
     * Retrieve price-to value from price filter value
     *
     * @param ItemInterface $filterItem
     * @return float
     */
    public function getPriceToValue($filterItem)
    {
        if ($this->filterChecker->isPrice($filterItem->getFilter())) {
            $interval = $this->filterIntervalResolver->getInterval($filterItem->getValue());
            return $interval ? $interval->getTo() : 0;
        } else {
            return 0;
        }
    }
}
