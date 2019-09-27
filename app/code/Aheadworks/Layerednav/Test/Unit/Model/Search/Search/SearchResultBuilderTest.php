<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Merger as AggregationMerger;
use Aheadworks\Layerednav\Model\Search\Search\SearchResultBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Search\ResponseInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\SearchResultBuilder
 */
class SearchResultBuilderTest extends TestCase
{
    /**
     * @var SearchResultBuilder
     */
    private $model;

    /**
     * @var AggregationMerger|\PHPUnit_Framework_MockObject_MockObject
     */
    private $aggregationMergerMock;

    /**
     * @var SearchResultFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->aggregationMergerMock = $this->createMock(AggregationMerger::class);
        $this->searchResultFactoryMock = $this->createMock(SearchResultFactory::class);

        $this->model = $objectManager->getObject(
            SearchResultBuilder::class,
            [
                'aggregationMerger' => $this->aggregationMergerMock,
                'searchResultFactory' => $this->searchResultFactoryMock
            ]
        );
    }

    /**
     * Test build method for Magento 2.3.1 version
     */
    public function testBuild231()
    {
        $documents = [
            $this->createMock(DocumentInterface::class),
            $this->createMock(DocumentInterface::class)
        ];
        $aggregationMock = $this->createMock(AggregationInterface::class);
        $searchResponseMock = $this->getSearchResponseMock231($documents, $aggregationMock);

        $additionalAggregations = [
            $this->createMock(AggregationInterface::class),
            $this->createMock(AggregationInterface::class)
        ];
        $mergedAggregation = $this->createMock(AggregationInterface::class);

        $this->aggregationMergerMock->expects($this->once())
            ->method('merge')
            ->with($aggregationMock, $additionalAggregations)
            ->willReturn($mergedAggregation);

        $searchResultMock = $this->getSearchResultMock($documents, $mergedAggregation, count($documents));
        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultMock);

        $this->assertSame($searchResultMock, $this->model->build($searchResponseMock, $additionalAggregations));
    }

    /**
     * Test build method if base aggregation specified for Magento 2.3.1 version
     */
    public function testBuildBaseAggregation231()
    {
        $documents = [
            $this->createMock(DocumentInterface::class),
            $this->createMock(DocumentInterface::class)
        ];
        $aggregationMock = $this->createMock(AggregationInterface::class);
        $searchResponseMock = $this->getSearchResponseMock231($documents, $aggregationMock);

        $additionalAggregations = [
            $this->createMock(AggregationInterface::class),
            $this->createMock(AggregationInterface::class)
        ];
        $mergedAggregation = $this->createMock(AggregationInterface::class);
        $baseAggregationMock = $this->createMock(AggregationInterface::class);
        $mergedBaseAggregationMock = $this->createMock(AggregationInterface::class);

        $aggregationsMap = [
            [$aggregationMock, $additionalAggregations, false, $mergedAggregation],
            [$baseAggregationMock, [$mergedAggregation], true, $mergedBaseAggregationMock]
        ];
        $this->aggregationMergerMock->expects($this->exactly(2))
            ->method('merge')
            ->will($this->returnValueMap($aggregationsMap));

        $searchResultMock = $this->getSearchResultMock($documents, $mergedAggregation, count($documents));
        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultMock);

        $this->assertSame(
            $searchResultMock,
            $this->model->build($searchResponseMock, $additionalAggregations, $baseAggregationMock)
        );
    }

    /**
     * Get search response mock for Magento 2.3.1
     *
     * @param DocumentInterface[]|\PHPUnit\Framework\MockObject\MockObject[] $documents
     * @param AggregationInterface|\PHPUnit\Framework\MockObject\MockObject $aggregationMock
     * @return ResponseInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getSearchResponseMock231(array $documents, $aggregationMock)
    {
        $searchResponseMock = $this->createMock(ResponseInterface::class);
        $searchResponseMock->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($documents));
        $searchResponseMock->expects($this->any())
            ->method('getAggregations')
            ->willReturn($aggregationMock);

        return $searchResponseMock;
    }

    /**
     * Get search result mock
     *
     * @param DocumentInterface[]|\PHPUnit\Framework\MockObject\MockObject[] $documents
     * @param AggregationInterface|\PHPUnit\Framework\MockObject\MockObject $mergedAggregation
     * @param int $totalCount
     * @return SearchResultInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getSearchResultMock(array $documents, $mergedAggregation, $totalCount)
    {
        $searchResultMock = $this->createMock(SearchResultInterface::class);
        $searchResultMock->expects($this->once())
            ->method('setItems')
            ->with($documents)
            ->willReturnSelf();
        $searchResultMock->expects($this->once())
            ->method('setAggregations')
            ->with($mergedAggregation)
            ->willReturnSelf();
        $searchResultMock->expects($this->once())
            ->method('setTotalCount')
            ->with($totalCount)
            ->willReturnSelf();

        return $searchResultMock;
    }

    /**
     * Test build method for Magento 2.3.2 version
     */
    public function testBuild232()
    {
        $documents = [
            $this->createMock(DocumentInterface::class),
            $this->createMock(DocumentInterface::class)
        ];
        $totalCount = 110;
        $aggregationMock = $this->createMock(AggregationInterface::class);
        $searchResponseMock = $this->getSearchResponseMock232($documents, $aggregationMock, $totalCount);

        $additionalAggregations = [
            $this->createMock(AggregationInterface::class),
            $this->createMock(AggregationInterface::class)
        ];
        $mergedAggregation = $this->createMock(AggregationInterface::class);

        $this->aggregationMergerMock->expects($this->once())
            ->method('merge')
            ->with($aggregationMock, $additionalAggregations)
            ->willReturn($mergedAggregation);

        $searchResultMock = $this->getSearchResultMock($documents, $mergedAggregation, $totalCount);
        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultMock);

        $this->assertSame($searchResultMock, $this->model->build($searchResponseMock, $additionalAggregations));
    }

    /**
     * Test build method if base aggregation specified for Magento 2.3.2 version
     */
    public function testBuildBaseAggregation232()
    {
        $documents = [
            $this->createMock(DocumentInterface::class),
            $this->createMock(DocumentInterface::class)
        ];
        $totalCount = 110;
        $aggregationMock = $this->createMock(AggregationInterface::class);
        $searchResponseMock = $this->getSearchResponseMock232($documents, $aggregationMock, $totalCount);

        $additionalAggregations = [
            $this->createMock(AggregationInterface::class),
            $this->createMock(AggregationInterface::class)
        ];
        $mergedAggregation = $this->createMock(AggregationInterface::class);
        $baseAggregationMock = $this->createMock(AggregationInterface::class);
        $mergedBaseAggregationMock = $this->createMock(AggregationInterface::class);

        $aggregationsMap = [
            [$aggregationMock, $additionalAggregations, false, $mergedAggregation],
            [$baseAggregationMock, [$mergedAggregation], true, $mergedBaseAggregationMock]
        ];
        $this->aggregationMergerMock->expects($this->exactly(2))
            ->method('merge')
            ->will($this->returnValueMap($aggregationsMap));

        $searchResultMock = $this->getSearchResultMock($documents, $mergedAggregation, $totalCount);
        $this->searchResultFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultMock);

        $this->assertSame(
            $searchResultMock,
            $this->model->build($searchResponseMock, $additionalAggregations, $baseAggregationMock)
        );
    }

    /**
     * Get search response mock for Magento 2.3.2
     *
     * @param DocumentInterface[]|\PHPUnit\Framework\MockObject\MockObject[] $documents
     * @param AggregationInterface|\PHPUnit\Framework\MockObject\MockObject $aggregationMock
     * @param int $totalCount
     * @return ResponseInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getSearchResponseMock232(array $documents, $aggregationMock, $totalCount)
    {
        $searchResponseMock = $this->getMockForAbstractClass(
            ResponseInterface::class,
            [],
            '',
            false,
            false,
            false,
            [
                'getIterator',
                'getAggregations',
                'getTotal',
            ]
        );
        $searchResponseMock->expects($this->any())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator($documents));
        $searchResponseMock->expects($this->any())
            ->method('getAggregations')
            ->willReturn($aggregationMock);
        $searchResponseMock->expects($this->any())
            ->method('getTotal')
            ->willReturn($totalCount);

        return $searchResponseMock;
    }
}
