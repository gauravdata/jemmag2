<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search;

use Aheadworks\Layerednav\Model\Search\Filter\Checker as FilterChecker;

/**
 * Class Checker
 * @package Aheadworks\Layerednav\Model\Search
 */
class Checker
{
    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @param FilterChecker $filterChecker
     */
    public function __construct(
        FilterChecker $filterChecker
    ) {
        $this->filterChecker = $filterChecker;
    }

    /**
     * Check if extended search is needed
     *
     * @return bool
     */
    public function isExtendedSearchNeeded()
    {
        return $this->filterChecker->hasAppliedFilters();
    }

    /**
     * Check if category filter applied
     *
     * @return bool
     */
    public function isCategoryFilterApplied()
    {
        if ($this->filterChecker->isApplied('category_ids_query')) {
            return true;
        }

        return false;
    }
}
