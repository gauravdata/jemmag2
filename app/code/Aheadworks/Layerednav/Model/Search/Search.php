<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Search\Filter\State as FilterState;
use Aheadworks\Layerednav\Model\Search\Checker as FilterStateChecker;
use Aheadworks\Layerednav\Model\Search\Search\BaseAggregationBuilder;
use Aheadworks\Layerednav\Model\Search\Search\ExtendedAggregationsBuilder;
use Aheadworks\Layerednav\Model\Search\Search\ResponseBuilder as SearchResponseBuilder;
use Aheadworks\Layerednav\Model\Search\Search\SearchResultBuilder;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\ResponseInterface;

/**
 * Class Search
 * @package Aheadworks\Layerednav\Model\Search
 */
class Search implements SearchInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * @var FilterState
     */
    private $filterState;

    /**
     * @var FilterStateChecker
     */
    private $filterStateChecker;

    /**
     * @var BaseAggregationBuilder
     */
    private $baseAggregationBuilder;

    /**
     * @var ExtendedAggregationsBuilder
     */
    private $extendedAggregationsBuilder;

    /**
     * @var SearchResponseBuilder
     */
    private $searchResponseBuilder;

    /**
     * @var SearchResultBuilder
     */
    private $searchResultBuilder;

    /**
     * @param Config $config
     * @param ScopeResolverInterface $scopeResolver
     * @param FilterState $filterState
     * @param FilterStateChecker $filterStateChecker
     * @param BaseAggregationBuilder $baseAggregationBuilder
     * @param ExtendedAggregationsBuilder $extendedAggregationsBuilder
     * @param SearchResponseBuilder $searchResponseBuilder
     * @param SearchResultBuilder $searchResultBuilder
     */
    public function __construct(
        Config $config,
        ScopeResolverInterface $scopeResolver,
        FilterState $filterState,
        FilterStateChecker $filterStateChecker,
        BaseAggregationBuilder $baseAggregationBuilder,
        ExtendedAggregationsBuilder $extendedAggregationsBuilder,
        SearchResponseBuilder $searchResponseBuilder,
        SearchResultBuilder $searchResultBuilder
    ) {
        $this->config = $config;
        $this->scopeResolver = $scopeResolver;
        $this->filterState = $filterState;
        $this->filterStateChecker = $filterStateChecker;
        $this->baseAggregationBuilder = $baseAggregationBuilder;
        $this->extendedAggregationsBuilder = $extendedAggregationsBuilder;
        $this->searchResponseBuilder = $searchResponseBuilder;
        $this->searchResultBuilder = $searchResultBuilder;
    }

    /**
     * {@inheritdoc}
     * @throws StateException
     */
    public function search(SearchCriteriaInterface $searchCriteria)
    {
        $scope = $this->scopeResolver->getScope()->getId();

        $baseAggregations = null;
        if (!$this->config->hideEmptyAttributeValues()) {
            /** @var AggregationInterface $baseAggregations */
            $baseAggregations = $this->baseAggregationBuilder->build($searchCriteria, $scope);
        }

        // Prevents to add base category to where condition
        // Workaround due to complexity of Mysql engine query generation
        if ($this->filterStateChecker->isCategoryFilterApplied()) {
            $this->filterState->setDoNotUseBaseCategoryFlag();
        }
        /** @var AggregationInterface[] $extendedAggregations */
        $extendedAggregations = $this->extendedAggregationsBuilder->build($searchCriteria, $scope);

        /** @var ResponseInterface $searchResponse */
        $searchResponse = $this->searchResponseBuilder->build($searchCriteria, $scope);
        $this->filterState->reset();

        /** @var SearchResultInterface $result */
        $result = $this->searchResultBuilder->build($searchResponse, $extendedAggregations, $baseAggregations)
            ->setSearchCriteria($searchCriteria);

        return $result;
    }
}
