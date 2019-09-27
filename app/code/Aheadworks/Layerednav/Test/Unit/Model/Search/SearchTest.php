<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Search\Checker as FilterStateChecker;
use Aheadworks\Layerednav\Model\Search\Filter\State as FilterState;
use Aheadworks\Layerednav\Model\Search\Search;
use Aheadworks\Layerednav\Model\Search\Search\BaseAggregationBuilder;
use Aheadworks\Layerednav\Model\Search\Search\ExtendedAggregationsBuilder;
use Aheadworks\Layerednav\Model\Search\Search\ResponseBuilder as SearchResponseBuilder;
use Aheadworks\Layerednav\Model\Search\Search\SearchResultBuilder;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Search\ResponseInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\ScopeInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search
 */
class SearchTest extends TestCase
{
    /**
     * @var Search
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var ScopeResolverInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeResolverMock;

    /**
     * @var FilterState|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterStateMock;

    /**
     * @var FilterStateChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterStateCheckerMock;

    /**
     * @var BaseAggregationBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $baseAggregationBuilderMock;

    /**
     * @var ExtendedAggregationsBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extendedAggregationsBuilderMock;

    /**
     * @var SearchResponseBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResponseBuilderMock;

    /**
     * @var SearchResultBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchResultBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->scopeResolverMock = $this->createMock(ScopeResolverInterface::class);
        $this->filterStateMock = $this->createMock(FilterState::class);
        $this->filterStateCheckerMock = $this->createMock(FilterStateChecker::class);
        $this->baseAggregationBuilderMock = $this->createMock(BaseAggregationBuilder::class);
        $this->extendedAggregationsBuilderMock = $this->createMock(ExtendedAggregationsBuilder::class);
        $this->searchResponseBuilderMock = $this->createMock(SearchResponseBuilder::class);
        $this->searchResultBuilderMock = $this->createMock(SearchResultBuilder::class);

        $this->model = $objectManager->getObject(
            Search::class,
            [
                'config' => $this->configMock,
                'scopeResolver' => $this->scopeResolverMock,
                'filterState' => $this->filterStateMock,
                'filterStateChecker' => $this->filterStateCheckerMock,
                'baseAggregationBuilder' => $this->baseAggregationBuilderMock,
                'extendedAggregationsBuilder' => $this->extendedAggregationsBuilderMock,
                'searchResponseBuilder' => $this->searchResponseBuilderMock,
                'searchResultBuilder' => $this->searchResultBuilderMock,
            ]
        );
    }

    /**
     * Test search method
     *
     * @param bool $hideEmptyAttributeValues
     * @param bool $isCategoryFilterApplied
     * @dataProvider searchDataProvider
     * @throws StateException
     */
    public function testSearch($hideEmptyAttributeValues, $isCategoryFilterApplied)
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $scopeId = 1;
        $this->setScope($scopeId);

        $this->configMock->expects($this->once())
            ->method('hideEmptyAttributeValues')
            ->willReturn($hideEmptyAttributeValues);

        if (!$hideEmptyAttributeValues) {
            $baseAggregationMock = $this->createMock(AggregationInterface::class);
            $this->baseAggregationBuilderMock->expects($this->once())
                ->method('build')
                ->with($searchCriteriaMock, $scopeId)
                ->willReturn($baseAggregationMock);
        } else {
            $baseAggregationMock = null;
            $this->baseAggregationBuilderMock->expects($this->never())
                ->method('build');
        }

        $this->filterStateCheckerMock->expects($this->once())
            ->method('isCategoryFilterApplied')
            ->willReturn($isCategoryFilterApplied);
        if ($isCategoryFilterApplied) {
            $this->filterStateMock->expects($this->once())
                ->method('setDoNotUseBaseCategoryFlag');
        } else {
            $this->filterStateMock->expects($this->never())
                ->method('setDoNotUseBaseCategoryFlag');
        }
        $this->filterStateMock->expects($this->once())
            ->method('reset');

        $extendedAggregations = [
            $this->createMock(AggregationInterface::class),
            $this->createMock(AggregationInterface::class)
        ];
        $this->extendedAggregationsBuilderMock->expects($this->once())
            ->method('build')
            ->with($searchCriteriaMock, $scopeId)
            ->willReturn($extendedAggregations);

        $searchResponseMock = $this->createMock(ResponseInterface::class);
        $this->searchResponseBuilderMock->expects($this->once())
            ->method('build')
            ->with($searchCriteriaMock, $scopeId)
            ->willReturn($searchResponseMock);

        $searchResultMock = $this->createMock(SearchResultInterface::class);
        $searchResultMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();

        $this->searchResultBuilderMock->expects($this->once())
            ->method('build')
            ->with($searchResponseMock, $extendedAggregations, $baseAggregationMock)
            ->willReturn($searchResultMock);

        $this->assertSame($searchResultMock, $this->model->search($searchCriteriaMock));
    }

    /**
     * @return array
     */
    public function searchDataProvider()
    {
        return [
            [
                'hideEmptyAttributeValues' => true,
                'isCategoryFilterApplied' => false,
            ],
            [
                'hideEmptyAttributeValues' => false,
                'isCategoryFilterApplied' => false,
            ],
            [
                'hideEmptyAttributeValues' => true,
                'isCategoryFilterApplied' => true,
            ],
            [
                'hideEmptyAttributeValues' => false,
                'isCategoryFilterApplied' => true,
            ]
        ];
    }

    /**
     * Test search method if an exception occurs for base aggregation
     *
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Error!
     */
    public function testSearchBaseException()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $scopeId = 1;
        $this->setScope($scopeId);

        $this->configMock->expects($this->once())
            ->method('hideEmptyAttributeValues')
            ->willReturn(false);

        $this->baseAggregationBuilderMock->expects($this->once())
            ->method('build')
            ->with($searchCriteriaMock, $scopeId)
            ->willThrowException(new StateException(__('Error!')));

        $this->model->search($searchCriteriaMock);
    }

    /**
     * Test search method if an exception occurs for extended aggregation
     *
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Error!
     */
    public function testSearchExtendedException()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $scopeId = 1;
        $this->setScope($scopeId);

        $this->configMock->expects($this->once())
            ->method('hideEmptyAttributeValues')
            ->willReturn(true);

        $this->filterStateCheckerMock->expects($this->once())
            ->method('isCategoryFilterApplied')
            ->willReturn(false);

        $this->extendedAggregationsBuilderMock->expects($this->once())
            ->method('build')
            ->with($searchCriteriaMock, $scopeId)
            ->willThrowException(new StateException(__('Error!')));

        $this->model->search($searchCriteriaMock);
    }

    /**
     * Test search method if an exception occurs for the response builder
     *
     * @expectedException \Magento\Framework\Exception\StateException
     * @expectedExceptionMessage Error!
     */
    public function testSearchResponseException()
    {
        /** @var SearchCriteriaInterface|\PHPUnit_Framework_MockObject_MockObject $searchCriteriaMock */
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $scopeId = 1;
        $this->setScope($scopeId);

        $this->configMock->expects($this->once())
            ->method('hideEmptyAttributeValues')
            ->willReturn(true);

        $this->filterStateCheckerMock->expects($this->once())
            ->method('isCategoryFilterApplied')
            ->willReturn(false);

        $extendedAggregations = [
            $this->createMock(AggregationInterface::class),
            $this->createMock(AggregationInterface::class)
        ];
        $this->extendedAggregationsBuilderMock->expects($this->once())
            ->method('build')
            ->with($searchCriteriaMock, $scopeId)
            ->willReturn($extendedAggregations);

        $this->searchResponseBuilderMock->expects($this->once())
            ->method('build')
            ->with($searchCriteriaMock, $scopeId)
            ->willThrowException(new StateException(__('Error!')));

        $this->model->search($searchCriteriaMock);
    }

    /**
     * Set scope
     *
     * @param int $scopeId
     */
    private function setScope($scopeId)
    {
        $scopeMock = $this->createMock(ScopeInterface::class);
        $scopeMock->expects($this->once())
            ->method('getId')
            ->willReturn($scopeId);
        $this->scopeResolverMock->expects($this->once())
            ->method('getScope')
            ->willReturn($scopeMock);
    }
}
