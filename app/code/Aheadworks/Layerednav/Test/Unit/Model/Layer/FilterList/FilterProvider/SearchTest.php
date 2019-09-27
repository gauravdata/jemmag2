<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\FilterList\FilterProvider\Search;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\FilterList\FilterProvider\Search
 */
class SearchTest extends TestCase
{
    /**
     * @var Search
     */
    private $model;

    /**
     * @var FilterRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var SortOrderBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $sortOrderBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterRepositoryMock = $this->createMock(FilterRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->sortOrderBuilderMock = $this->createMock(SortOrderBuilder::class);

        $this->model = $objectManager->getObject(
            Search::class,
            [
                'filterRepository' => $this->filterRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'sortOrderBuilder' => $this->sortOrderBuilderMock
            ]
        );
    }

    /**
     * Test getFilterDataObjects method
     *
     * @param array $items
     * @param array $expectedResult
     * @dataProvider getFilterDataObjectsDataProvider
     * @throws \ReflectionException
     */
    public function testGetFilterDataObjects($items, $expectedResult)
    {
        $sortOrderMock = $this->createMock(SortOrder::class);
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setField')
            ->with(FilterInterface::POSITION)
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('setAscendingDirection')
            ->willReturnSelf();
        $this->sortOrderBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderMock);

        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(FilterInterface::IS_FILTERABLE_IN_SEARCH, 0, 'gt')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addSortOrder')
            ->with($sortOrderMock)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $filterSearchResultsMock = $this->getMockBuilder(FilterSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $filterSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $this->filterRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($filterSearchResultsMock);

        $this->assertEquals($expectedResult, $this->model->getFilterDataObjects());
    }

    /**
     * @return array
     */
    public function getFilterDataObjectsDataProvider()
    {
        $filterObjectMock = $this->createMock(FilterInterface::class);

        return [
            [
                'items' => [],
                'expectedResult' => []
            ],
            [
                'items' => [$filterObjectMock],
                'expectedResult' => [$filterObjectMock]
            ],
        ];
    }
}
