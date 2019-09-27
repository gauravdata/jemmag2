<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket\NameResolver as BucketNameResolver;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\SearchEngineInterface;
use Magento\Framework\Exception\StateException;

/**
 * Class FieldAggregationBuilder
 * @package Aheadworks\Layerednav\Model\Search\Search
 */
class FieldAggregationBuilder
{
    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * @var SearchEngineInterface
     */
    private $searchEngine;

    /**
     * @var BucketNameResolver
     */
    private $bucketNameResolver;

    /**
     * @param RequestBuilder $requestBuilder
     * @param SearchEngineInterface $searchEngine
     * @param BucketNameResolver $bucketNameResolver
     */
    public function __construct(
        RequestBuilder $requestBuilder,
        SearchEngineInterface $searchEngine,
        BucketNameResolver $bucketNameResolver
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->searchEngine = $searchEngine;
        $this->bucketNameResolver = $bucketNameResolver;
    }

    /**
     * Build aggregations
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param string $filterName
     * @param string[] $fields
     * @param int $scope
     * @return AggregationInterface
     * @throws StateException
     */
    public function build(SearchCriteriaInterface $searchCriteria, $filterName, $fields, $scope)
    {
        $this->requestBuilder->setRequestName($searchCriteria->getRequestName());
        $this->requestBuilder->bindDimension('scope', $scope);
        $this->requestBuilder->setFrom($searchCriteria->getCurrentPage() * $searchCriteria->getPageSize());
        $this->requestBuilder->setSize($searchCriteria->getPageSize());

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if (!in_array($filter->getField(), $fields)) {
                    $this->requestBuilder->addFieldToFilter($filter->getField(), $filter->getValue());
                }
            }
        }

        $bucketName = $this->bucketNameResolver->getName($filterName);
        $this->requestBuilder->setAllowedAggregations([$bucketName]);

        /** @var RequestInterface $request */
        $request = $this->requestBuilder->create();
        $searchResponse = $this->searchEngine->search($request);

        /** @var AggregationInterface $aggregations */
        $aggregations = $searchResponse->getAggregations();

        return $aggregations;
    }
}
