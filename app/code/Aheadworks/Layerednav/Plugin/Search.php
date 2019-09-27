<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin;

use Aheadworks\Layerednav\Model\Search\Checker as SearchChecker;
use Aheadworks\Layerednav\Model\Search\Search as ExtendedSearch;
use Magento\Framework\Api\Search\SearchInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Class Search
 * @package Aheadworks\Layerednav\Plugin
 */
class Search
{
    /**
     * @var SearchChecker
     */
    private $searchChecker;

    /**
     * @var ExtendedSearch
     */
    private $extendedSearch;

    /**
     * @param SearchChecker $searchChecker
     * @param ExtendedSearch $extendedSearch
     */
    public function __construct(
        SearchChecker $searchChecker,
        ExtendedSearch $extendedSearch
    ) {
        $this->searchChecker = $searchChecker;
        $this->extendedSearch = $extendedSearch;
    }

    /**
     * Modify search result
     *
     * @param SearchInterface $subject
     * @param \Closure $proceed
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     */
    public function aroundSearch(
        SearchInterface $subject,
        \Closure $proceed,
        SearchCriteriaInterface $searchCriteria
    ) {
        if ($this->searchChecker->isExtendedSearchNeeded()) {
            $result = $this->extendedSearch->search($searchCriteria);
        } else {
            $result = $proceed($searchCriteria);
        }

        return $result;
    }
}
