<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Search\FieldAggregationBuilder;
use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket\NameResolver as BucketNameResolver;
use Aheadworks\Layerednav\Model\Search\Search\RequestBuilder;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\ResponseInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Search\SearchEngineInterface;
use Magento\Framework\Exception\StateException;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\FieldAggregationBuilder
 */
class FieldAggregationBuilderTest extends TestCase
{
    /**
     * @var FieldAggregationBuilder
     */
    private $model;

    /**
     * @var RequestBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestBuilderMock;

    /**
     * @var SearchEngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchEngineMock;

    /**
     * @var BucketNameResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bucketNameResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestBuilderMock = $this->createMock(RequestBuilder::class);
        $this->searchEngineMock = $this->createMock(SearchEngineInterface::class);
        $this->bucketNameResolverMock = $this->createMock(BucketNameResolver::class);

        $this->model = $objectManager->getObject(
            FieldAggregationBuilder::class,
            [
                'requestBuilder' => $this->requestBuilderMock,
                'searchEngine' => $this->searchEngineMock,
                'bucketNameResolver' => $this->bucketNameResolverMock,
            ]
        );
    }

    /**
     * Test build method
     */
    public function testBuild()
    {
        $requestName = 'search_request';
        $currentPage = 10;
        $pageSize = 5;
        $extendedFilterName = 'extended_filter';
        $extendedFilterField = 'extended_filter_field';
        $extendedFilterValue = 'extended_filter_value';
        $filterField = 'filter_field';
        $filterValue = 'filter_value';
        $scope = '1';
        $extendedBucketName = 'filter_field_bucket';

        $extendedFilterMock = $this->getFilterMock($extendedFilterField, $extendedFilterValue);
        $filterMock = $this->getFilterMock($filterField, $filterValue);
        $filterGroups = [
            $this->getFilterGroupMock([$extendedFilterMock]),
            $this->getFilterGroupMock([$filterMock])
        ];
        $searchCriteriaMock = $this->getSearchCriteriaMock($requestName, $currentPage, $pageSize, $filterGroups);

        $this->bucketNameResolverMock->expects($this->once())
            ->method('getName')
            ->with($extendedFilterName)
            ->willReturn($extendedBucketName);

        $requestMock = $this->createMock(RequestInterface::class);
        $this->setRequestBuilderParams($requestName, $scope, $currentPage, $pageSize);
        $this->requestBuilderMock->expects($this->once())
            ->method('setAllowedAggregations')
            ->with([$extendedBucketName])
            ->willReturnSelf();
        $this->requestBuilderMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with($filterField, $filterValue)
            ->willReturnSelf();
        $this->requestBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($requestMock);

        $aggregationMock = $this->createMock(AggregationInterface::class);
        $searchResponseMock = $this->createMock(ResponseInterface::class);
        $searchResponseMock->expects($this->once())
            ->method('getAggregations')
            ->willReturn($aggregationMock);
        $this->searchEngineMock->expects($this->once())
            ->method('search')
            ->with($requestMock)
            ->willReturn($searchResponseMock);

        $this->assertEquals(
            $aggregationMock,
            $this->model->build($searchCriteriaMock, $extendedFilterName, [$extendedFilterField], $scope)
        );
    }

    /**
     * Test build method if an exception occurs
     *
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Error!
     */
    public function testBuildException()
    {
        $requestName = 'search_request';
        $currentPage = 10;
        $pageSize = 5;
        $extendedFilterName = 'extended_filter';
        $extendedFilterField = 'extended_filter_field';
        $extendedFilterValue = 'extended_filter_value';
        $filterField = 'filter_field';
        $filterValue = 'filter_value';
        $scope = '1';
        $extendedBucketName = 'filter_field_bucket';

        $extendedFilterMock = $this->getFilterMock($extendedFilterField, $extendedFilterValue);
        $filterMock = $this->getFilterMock($filterField, $filterValue);
        $filterGroups = [
            $this->getFilterGroupMock([$extendedFilterMock]),
            $this->getFilterGroupMock([$filterMock])
        ];
        $searchCriteriaMock = $this->getSearchCriteriaMock($requestName, $currentPage, $pageSize, $filterGroups);

        $this->bucketNameResolverMock->expects($this->once())
            ->method('getName')
            ->with($extendedFilterName)
            ->willReturn($extendedBucketName);

        $this->setRequestBuilderParams($requestName, $scope, $currentPage, $pageSize);
        $this->requestBuilderMock->expects($this->once())
            ->method('setAllowedAggregations')
            ->with([$extendedBucketName])
            ->willReturnSelf();
        $this->requestBuilderMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with($filterField, $filterValue)
            ->willReturnSelf();
        $this->requestBuilderMock->expects($this->once())
            ->method('create')
            ->willThrowException(new StateException(__('Error!')));

        $this->searchEngineMock->expects($this->never())
            ->method('search');

        $this->model->build($searchCriteriaMock, $extendedFilterName, [$extendedFilterField], $scope);
    }

    /**
     * Get filter mock
     *
     * @param string $field
     * @param string|string[] $value
     * @return Filter|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getFilterMock($field, $value)
    {
        $filterMock = $this->createMock(Filter::class);
        $filterMock->expects($this->any())
            ->method('getField')
            ->willReturn($field);
        $filterMock->expects($this->any())
            ->method('getValue')
            ->willReturn($value);

        return $filterMock;
    }

    /**
     * Get filter group mock
     *
     * @param Filter[]|\PHPUnit\Framework\MockObject\MockObject[] $filters
     * @return FilterGroup|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getFilterGroupMock(array $filters)
    {
        $filterFroupMock = $this->createMock(FilterGroup::class);
        $filterFroupMock->expects($this->any())
            ->method('getFilters')
            ->willReturn($filters);

        return $filterFroupMock;
    }

    /**
     * Get search criteris mock
     *
     * @param string $requestName
     * @param int $currentPage
     * @param int $pageSize
     * @param FilterGroup[]|\PHPUnit\Framework\MockObject\MockObject[] $filterGroups
     * @return SearchCriteriaInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getSearchCriteriaMock($requestName, $currentPage, $pageSize, array $filterGroups)
    {
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);
        $searchCriteriaMock->expects($this->any())
            ->method('getRequestName')
            ->willReturn($requestName);
        $searchCriteriaMock->expects($this->any())
            ->method('getCurrentPage')
            ->willReturn($currentPage);
        $searchCriteriaMock->expects($this->any())
            ->method('getPageSize')
            ->willReturn($pageSize);
        $searchCriteriaMock->expects($this->any())
            ->method('getFilterGroups')
            ->willReturn($filterGroups);

        return $searchCriteriaMock;
    }

    /**
     * Set request builder parameters
     *
     * @param string $requestName
     * @param string $scope
     * @param int $currentPage
     * @param int $pageSize
     */
    private function setRequestBuilderParams($requestName, $scope, $currentPage, $pageSize)
    {
        $this->requestBuilderMock->expects($this->once())
            ->method('setRequestName')
            ->with($requestName)
            ->willReturnSelf();
        $this->requestBuilderMock->expects($this->once())
            ->method('bindDimension')
            ->with('scope', $scope)
            ->willReturnSelf();
        $this->requestBuilderMock->expects($this->once())
            ->method('setFrom')
            ->with($currentPage * $pageSize)
            ->willReturnSelf();
        $this->requestBuilderMock->expects($this->once())
            ->method('setSize')
            ->with($pageSize)
            ->willReturnSelf();
    }
}
