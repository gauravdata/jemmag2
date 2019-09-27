<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Filter\Checker as FilterChecker;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\StateException;

/**
 * Class ExtendedAggregationsBuilder
 * @package Aheadworks\Layerednav\Model\Search\Search
 */
class ExtendedAggregationsBuilder
{
    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var FieldAggregationBuilder
     */
    private $fieldAggregationBuilder;

    /**
     * @param FilterChecker $filterChecker
     * @param FieldAggregationBuilder $fieldAggregationBuilder
     */
    public function __construct(
        FilterChecker $filterChecker,
        FieldAggregationBuilder $fieldAggregationBuilder
    ) {
        $this->filterChecker = $filterChecker;
        $this->fieldAggregationBuilder = $fieldAggregationBuilder;
    }

    /**
     * Build extended aggregations
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param $scope
     * @return AggregationInterface[]
     * @throws StateException
     */
    public function build(SearchCriteriaInterface $searchCriteria, $scope)
    {
        $extendedFilters = $this->filterChecker->getExtendedFilters();

        $extendedAggregations = [];
        foreach ($extendedFilters as $filterName => $fields) {
            $fieldAggregations =  $this->fieldAggregationBuilder->build($searchCriteria, $filterName, $fields, $scope);
            $extendedAggregations[] = $fieldAggregations;
        }

        return $extendedAggregations;
    }
}
