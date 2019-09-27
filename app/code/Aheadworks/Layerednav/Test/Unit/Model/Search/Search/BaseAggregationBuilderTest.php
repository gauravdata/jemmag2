<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Search\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Search\Search\BaseAggregationBuilder;
use Aheadworks\Layerednav\Model\Search\Search\RequestBuilder;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\ResponseInterface;
use Magento\Framework\Search\SearchEngineInterface;
use Magento\Framework\Exception\StateException;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\BaseAggregationBuilder
 */
class BaseAggregationBuilderTest extends TestCase
{
    /**
     * @var BaseAggregationBuilder
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var RequestBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $requestBuilderMock;

    /**
     * @var SearchEngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchEngineMock;

    /**
     * @var FilterChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCheckerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->requestBuilderMock = $this->createMock(RequestBuilder::class);
        $this->searchEngineMock = $this->createMock(SearchEngineInterface::class);
        $this->filterCheckerMock = $this->createMock(FilterChecker::class);

        $this->model = $objectManager->getObject(
            BaseAggregationBuilder::class,
            [
                'config' => $this->configMock,
                'requestBuilder' => $this->requestBuilderMock,
                'searchEngine' => $this->searchEngineMock,
                'filterChecker' => $this->filterCheckerMock,
            ]
        );
    }

    /**
     * Test build method
     */
    public function testBuild()
    {
        $requestName = 'search_request';
        $baseRequestName = 'search_request_base';
        $currentPage = 10;
        $pageSize = 5;
        $baseFilterField = 'base_filter_field';
        $baseFilterValue = 'base_filter_value';
        $filterField = 'filter_field';
        $filterValue = 'filter_value';
        $scope = '1';

        $baseFilterMock = $this->getFilterMock($baseFilterField, $baseFilterValue);
        $appliedFilterMock = $this->getFilterMock($filterField, $filterValue);
        $filterGroups = [
            $this->getFilterGroupMock([$baseFilterMock]),
            $this->getFilterGroupMock([$appliedFilterMock])
        ];
        $searchCriteriaMock = $this->getSearchCriteriaMock($requestName, $currentPage, $pageSize, $filterGroups);

        $appliedFiltersMap = [
            [$baseFilterField, false],
            [$filterField, true],
        ];
        $this->filterCheckerMock->expects($this->exactly(2))
            ->method('isApplied')
            ->will($this->returnValueMap($appliedFiltersMap));

        $requestMock = $this->createMock(RequestInterface::class);
        $this->setRequestBuilderParams($baseRequestName, $scope, $currentPage, $pageSize);
        $this->requestBuilderMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with($baseFilterField, $baseFilterValue)
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

        $this->assertEquals($aggregationMock, $this->model->build($searchCriteriaMock, $scope));
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
        $baseRequestName = 'search_request_base';
        $currentPage = 10;
        $pageSize = 5;
        $baseFilterField = 'base_filter_field';
        $baseFilterValue = 'base_filter_value';
        $filterField = 'filter_field';
        $filterValue = 'filter_value';
        $scope = '1';

        $baseFilterMock = $this->getFilterMock($baseFilterField, $baseFilterValue);
        $appliedFilterMock = $this->getFilterMock($filterField, $filterValue);
        $filterGroups = [
            $this->getFilterGroupMock([$baseFilterMock]),
            $this->getFilterGroupMock([$appliedFilterMock])
        ];
        $searchCriteriaMock = $this->getSearchCriteriaMock($requestName, $currentPage, $pageSize, $filterGroups);

        $appliedFiltersMap = [
            [$baseFilterField, false],
            [$filterField, true],
        ];
        $this->filterCheckerMock->expects($this->exactly(2))
            ->method('isApplied')
            ->will($this->returnValueMap($appliedFiltersMap));

        $this->setRequestBuilderParams($baseRequestName, $scope, $currentPage, $pageSize);
        $this->requestBuilderMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with($baseFilterField, $baseFilterValue)
            ->willReturnSelf();
        $this->requestBuilderMock->expects($this->once())
            ->method('create')
            ->willThrowException(new StateException(__('Error!')));

        $this->searchEngineMock->expects($this->never())
            ->method('search');

        $this->model->build($searchCriteriaMock, $scope);
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
     * @throws \ReflectionException
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
