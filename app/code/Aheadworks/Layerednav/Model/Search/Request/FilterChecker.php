<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Request;

use Magento\Framework\Search\Request\FilterInterface;

/**
 * Class FilterChecker
 * @package Aheadworks\Layerednav\Model\Search\Request
 */
class FilterChecker
{
    /**
     * @var string
     */
    private $baseCategoryFilter;

    /**
     * @var string
     */
    private $categoryFilter;

    /**
     * @var string[]
     */
    private $customFilters;

    /**
     * @param string $baseCategoryFilter
     * @param string $categoryFilter
     * @param string[] $customFilters
     */
    public function __construct(
        $baseCategoryFilter = '',
        $categoryFilter = '',
        array $customFilters = []
    ) {
        $this->baseCategoryFilter = $baseCategoryFilter;
        $this->categoryFilter = $categoryFilter;
        $this->customFilters = $customFilters;
    }

    /**
     * Check if a filter specified is custom
     *
     * @param FilterInterface $filter
     * @return bool
     */
    public function isCustom(FilterInterface $filter)
    {
        if (in_array($filter->getName(), $this->customFilters)) {
            return true;
        }

        return false;
    }

    /**
     * Check if a filter specified is base category filter
     *
     * @param FilterInterface $filter
     * @return bool
     */
    public function isBaseCategory(FilterInterface $filter)
    {
        if ($filter->getName() == $this->baseCategoryFilter) {
            return true;
        }

        return false;
    }

    /**
     * Check if a filter specified is category filter
     *
     * @param FilterInterface $filter
     * @return bool
     */
    public function isCategory(FilterInterface $filter)
    {
        if ($filter->getName() == $this->categoryFilter) {
            return true;
        }

        return false;
    }
}
