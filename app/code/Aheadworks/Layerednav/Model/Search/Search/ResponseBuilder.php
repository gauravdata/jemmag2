<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search;

use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\ResponseInterface;
use Magento\Framework\Search\SearchEngineInterface;

/**
 * Class ResponseBuilder
 * @package Aheadworks\Layerednav\Model\Search\Search
 */
class ResponseBuilder
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
     * @param RequestBuilder $requestBuilder
     * @param SearchEngineInterface $searchEngine
     */
    public function __construct(
        RequestBuilder $requestBuilder,
        SearchEngineInterface $searchEngine
    ) {
        $this->requestBuilder = $requestBuilder;
        $this->searchEngine = $searchEngine;
    }

    /**
     * Build search response
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int $scope
     * @return ResponseInterface
     * @throws StateException
     */
    public function build(SearchCriteriaInterface $searchCriteria, $scope)
    {
        $this->requestBuilder->setRequestName($searchCriteria->getRequestName());
        $this->requestBuilder->bindDimension('scope', $scope);
        $this->requestBuilder->setFrom($searchCriteria->getCurrentPage() * $searchCriteria->getPageSize());
        $size = empty($searchCriteria->getPageSize()) ? null : $searchCriteria->getPageSize();
        $this->requestBuilder->setSize($size);

        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $this->requestBuilder->addFieldToFilter($filter->getField(), $filter->getValue());
            }
        }

        $this->requestBuilder->setSort($searchCriteria->getSortOrders());

        /** @var RequestInterface $request */
        $request = $this->requestBuilder->create();
        /** @var ResponseInterface $searchResponse */
        $searchResponse = $this->searchEngine->search($request);

        return $searchResponse;
    }
}
