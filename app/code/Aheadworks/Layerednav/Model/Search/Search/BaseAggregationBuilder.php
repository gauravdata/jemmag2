<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Search\Filter\Checker as FilterChecker;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Search\SearchEngineInterface;
use Magento\Framework\Exception\StateException;

/**
 * Class BaseAggregationBuilder
 * @package Aheadworks\Layerednav\Model\Search\Search
 */
class BaseAggregationBuilder
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var SearchEngineInterface
     */
    private $searchEngine;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @param Config $config
     * @param RequestBuilder $requestBuilder
     * @param SearchEngineInterface $searchEngine
     * @param FilterChecker $filterChecker
     */
    public function __construct(
        Config $config,
        RequestBuilder $requestBuilder,
        SearchEngineInterface $searchEngine,
        FilterChecker $filterChecker
    ) {
        $this->config = $config;
        $this->requestBuilder = $requestBuilder;
        $this->searchEngine = $searchEngine;
        $this->filterChecker = $filterChecker;
    }

    /**
     * Build aggregations
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $scope
     * @return AggregationInterface
     * @throws StateException
     */
    public function build(SearchCriteriaInterface $searchCriteria, $scope)
    {
        $this->requestBuilder->setRequestName($searchCriteria->getRequestName() . '_base');
        $this->requestBuilder->bindDimension('scope', $scope);
        $this->requestBuilder->setFrom($searchCriteria->getCurrentPage() * $searchCriteria->getPageSize());
        $this->requestBuilder->setSize($searchCriteria->getPageSize());

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if (!$this->filterChecker->isApplied($filter->getField())) {
                    $this->requestBuilder->addFieldToFilter($filter->getField(), $filter->getValue());
                }
            }
        }
        $request = $this->requestBuilder->create();
        $searchResponse = $this->searchEngine->search($request);

        return $searchResponse->getAggregations();
    }
}
