<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Merger as AggregationMerger;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Search\ResponseInterface;

/**
 * Class SearchResponseBuilder
 * @package Aheadworks\Layerednav\Model\Search\Search
 */
class SearchResultBuilder
{
    /**
     * @var AggregationMerger
     */
    private $aggregationMerger;

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @param AggregationMerger $aggregationMerger
     * @param SearchResultFactory $searchResultFactory
     */
    public function __construct(
        AggregationMerger $aggregationMerger,
        SearchResultFactory $searchResultFactory
    ) {
        $this->aggregationMerger = $aggregationMerger;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Build search result
     *
     * @param ResponseInterface $searchResponse
     * @param AggregationInterface[] $additionalAggregations
     * @param AggregationInterface|null $baseAggregation
     * @return SearchResultInterface
     */
    public function build($searchResponse, $additionalAggregations, $baseAggregation = null)
    {
        $mergedAggregations = $this->aggregationMerger->merge(
            $searchResponse->getAggregations(),
            $additionalAggregations
        );

        if ($baseAggregation) {
            $mergedAggregations = $this->aggregationMerger->merge(
                $baseAggregation,
                [$mergedAggregations],
                true
            );
        }

        /** @var SearchResultInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();

        $documents = iterator_to_array($searchResponse);
        $searchResult->setItems($documents);
        $searchResult->setAggregations($mergedAggregations);
        $totalCount =
            method_exists($searchResponse, 'getTotal')
                ? $searchResponse->getTotal()
                : count($documents)
        ;
        $searchResult->setTotalCount($totalCount);

        return $searchResult;
    }
}
