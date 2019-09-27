<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Search\RequestBuilder;
use Aheadworks\Layerednav\Model\Search\Search\ResponseBuilder;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\ResponseInterface;
use Magento\Framework\Search\SearchEngineInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\ResponseBuilder
 */
class ResponseBuilderTest extends TestCase
{
    /**
     * @var ResponseBuilder
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
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->requestBuilderMock = $this->createMock(RequestBuilder::class);
        $this->searchEngineMock = $this->createMock(SearchEngineInterface::class);

        $this->model = $objectManager->getObject(
            ResponseBuilder::class,
            [
                'requestBuilder' => $this->requestBuilderMock,
                'searchEngine' => $this->searchEngineMock,
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
        $filterFieldOne = 'filter_field1';
        $filterValueOne = 'filter_value1';
        $filterFieldTwo = 'filter_field2';
        $filterValueTwo = 'filter_value2';
        $scope = '1';

        $filterOneMock = $this->getFilterMock($filterFieldOne, $filterValueOne);
        $filterTwoMock = $this->getFilterMock($filterFieldTwo, $filterValueTwo);
        $filterGroups = [
            $this->getFilterGroupMock([$filterOneMock]),
            $this->getFilterGroupMock([$filterTwoMock])
        ];
        $sortOrders = [
            'price' => 'DESC',
            'entity_id' => 'DESC',
        ];
        $searchCriteriaMock = $this->getSearchCriteriaMock(
            $requestName,
            $currentPage,
            $pageSize,
            $filterGroups,
            $sortOrders
        );

        $requestMock = $this->createMock(RequestInterface::class);
        $this->setRequestBuilderParams($requestName, $scope, $currentPage, $pageSize, $sortOrders);

        $fieldsMap = [
            [$filterFieldOne, $filterValueOne, $this->requestBuilderMock],
            [$filterFieldTwo, $filterValueTwo, $this->requestBuilderMock]
        ];
        $this->requestBuilderMock->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->will($this->returnValueMap($fieldsMap));
        $this->requestBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($requestMock);

        $searchResponseMock = $this->createMock(ResponseInterface::class);
        $this->searchEngineMock->expects($this->once())
            ->method('search')
            ->with($requestMock)
            ->willReturn($searchResponseMock);

        $this->assertSame($searchResponseMock, $this->model->build($searchCriteriaMock, $scope));
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
        $filterFieldOne = 'filter_field1';
        $filterValueOne = 'filter_value1';
        $filterFieldTwo = 'filter_field2';
        $filterValueTwo = 'filter_value2';
        $scope = '1';

        $filterOneMock = $this->getFilterMock($filterFieldOne, $filterValueOne);
        $filterTwoMock = $this->getFilterMock($filterFieldTwo, $filterValueTwo);
        $filterGroups = [
            $this->getFilterGroupMock([$filterOneMock]),
            $this->getFilterGroupMock([$filterTwoMock])
        ];
        $sortOrders = [
            'price' => 'DESC',
            'entity_id' => 'DESC',
        ];
        $searchCriteriaMock = $this->getSearchCriteriaMock(
            $requestName,
            $currentPage,
            $pageSize,
            $filterGroups,
            $sortOrders
        );

        $this->setRequestBuilderParams($requestName, $scope, $currentPage, $pageSize, $sortOrders);

        $fieldsMap = [
            [$filterFieldOne, $filterValueOne, $this->requestBuilderMock],
            [$filterFieldTwo, $filterValueTwo, $this->requestBuilderMock]
        ];
        $this->requestBuilderMock->expects($this->exactly(2))
            ->method('addFieldToFilter')
            ->will($this->returnValueMap($fieldsMap));
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
     * @param array $sortOrders
     * @return SearchCriteriaInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getSearchCriteriaMock(
        $requestName,
        $currentPage,
        $pageSize,
        array $filterGroups,
        array $sortOrders
    ) {
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
        $searchCriteriaMock->expects($this->any())
            ->method('getSortOrders')
            ->willReturn($sortOrders);

        return $searchCriteriaMock;
    }

    /**
     * Set request builder parameters
     *
     * @param string $requestName
     * @param string $scope
     * @param int $currentPage
     * @param int $pageSize
     * @param array $sortOrders
     */
    private function setRequestBuilderParams($requestName, $scope, $currentPage, $pageSize, $sortOrders)
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
        $this->requestBuilderMock->expects($this->once())
            ->method('setSort')
            ->with($sortOrders)
            ->willReturnSelf();
    }
}
