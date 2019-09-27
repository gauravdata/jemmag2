<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin\CatalogSearch\Search;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\ContextBuilder;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\Context;
use Aheadworks\Layerednav\Model\Search\Request\FilterChecker;
use Aheadworks\Layerednav\Plugin\CatalogSearch\Search\IndexBuilder;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Magento\CatalogSearch\Model\Search\IndexBuilder as SearchIndexBuilder;
use Magento\CatalogSearch\Model\Search\FiltersExtractor;
use Psr\Log\LoggerInterface;

/**
 * Test for \Aheadworks\Layerednav\Plugin\CatalogSearch\Search\IndexBuilder
 */
class IndexBuilderTest extends TestCase
{
    /**
     * @var IndexBuilder
     */
    private $plugin;

    /**
     * @var FiltersExtractor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filtersExtractorMock;

    /**
     * @var FilterChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCheckerMock;

    /**
     * @var CustomFilterApplier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customFilterApplierMock;

    /**
     * @var ContextBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextBuilderMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filtersExtractorMock = $this->createMock(FiltersExtractor::class);
        $this->filterCheckerMock = $this->createMock(FilterChecker::class);
        $this->customFilterApplierMock = $this->createMock(CustomFilterApplier::class);
        $this->contextBuilderMock = $this->createMock(ContextBuilder::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->plugin = $objectManager->getObject(
            IndexBuilder::class,
            [
                'filtersExtractor' => $this->filtersExtractorMock,
                'filterChecker' => $this->filterCheckerMock,
                'customFilterApplier' => $this->customFilterApplierMock,
                'contextBuilder' => $this->contextBuilderMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Test afterBuild method
     */
    public function testAfterBuild()
    {
        $searchIndexBuilderMock = $this->createMock(SearchIndexBuilder::class);
        $selectMock = $this->createMock(Select::class);

        $queryMock = $this->createMock(QueryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getQuery')
            ->willReturn($queryMock);

        $customFilterMock = $this->createMock(FilterInterface::class);
        $notCustomFilterMock = $this->createMock(FilterInterface::class);
        $filters = [$customFilterMock, $notCustomFilterMock];

        $this->filtersExtractorMock->expects($this->once())
            ->method('extractFiltersFromQuery')
            ->with($queryMock)
            ->willReturn($filters);

        $filterCheckermap = [
            [$customFilterMock, true],
            [$notCustomFilterMock, false]
        ];
        $this->filterCheckerMock->expects($this->exactly(2))
            ->method('isCustom')
            ->will($this->returnValueMap($filterCheckermap));

        $contextMock = $this->createMock(Context::class);
        $this->contextBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($contextMock);

        $this->customFilterApplierMock->expects($this->once())
            ->method('apply')
            ->with($contextMock, $customFilterMock, $selectMock)
            ->willReturn($selectMock);

        $this->loggerMock->expects($this->never())
            ->method('critical');

        $this->assertSame($selectMock, $this->plugin->afterBuild($searchIndexBuilderMock, $selectMock, $requestMock));
    }

    /**
     * Test afterBuild method if no custom filters
     */
    public function testAfterBuildNoCustomFilters()
    {
        $searchIndexBuilderMock = $this->createMock(SearchIndexBuilder::class);
        $selectMock = $this->createMock(Select::class);

        $queryMock = $this->createMock(QueryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getQuery')
            ->willReturn($queryMock);

        $filterOneMock = $this->createMock(FilterInterface::class);
        $filterTwoMock = $this->createMock(FilterInterface::class);
        $filters = [$filterOneMock, $filterTwoMock];

        $this->filtersExtractorMock->expects($this->once())
            ->method('extractFiltersFromQuery')
            ->with($queryMock)
            ->willReturn($filters);

        $this->filterCheckerMock->expects($this->atLeastOnce())
            ->method('isCustom')
            ->willReturn(false);

        $this->contextBuilderMock->expects($this->never())
            ->method('build');

        $this->customFilterApplierMock->expects($this->never())
            ->method('apply');

        $this->loggerMock->expects($this->never())
            ->method('critical');

        $this->assertSame($selectMock, $this->plugin->afterBuild($searchIndexBuilderMock, $selectMock, $requestMock));
    }

    /**
     * Test afterBuild method if no filters
     */
    public function testAfterBuildNoFilters()
    {
        $searchIndexBuilderMock = $this->createMock(SearchIndexBuilder::class);
        $selectMock = $this->createMock(Select::class);

        $queryMock = $this->createMock(QueryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getQuery')
            ->willReturn($queryMock);

        $filters = [];

        $this->filtersExtractorMock->expects($this->once())
            ->method('extractFiltersFromQuery')
            ->with($queryMock)
            ->willReturn($filters);

        $this->filterCheckerMock->expects($this->never())
            ->method('isCustom');

        $this->contextBuilderMock->expects($this->never())
            ->method('build');

        $this->customFilterApplierMock->expects($this->never())
            ->method('apply');

        $this->loggerMock->expects($this->never())
            ->method('critical');

        $this->assertSame($selectMock, $this->plugin->afterBuild($searchIndexBuilderMock, $selectMock, $requestMock));
    }

    /**
     * Test afterBuild method if an error occurs
     */
    public function testAfterBuildError()
    {
        $errorMessage = 'Error!';
        $searchIndexBuilderMock = $this->createMock(SearchIndexBuilder::class);
        $selectMock = $this->createMock(Select::class);

        $queryMock = $this->createMock(QueryInterface::class);
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getQuery')
            ->willReturn($queryMock);

        $customFilterMock = $this->createMock(FilterInterface::class);
        $filters = [$customFilterMock];

        $this->filtersExtractorMock->expects($this->once())
            ->method('extractFiltersFromQuery')
            ->with($queryMock)
            ->willReturn($filters);

        $this->filterCheckerMock->expects($this->once(1))
            ->method('isCustom')
            ->with($customFilterMock)
            ->willReturn(true);

        $contextMock = $this->createMock(Context::class);
        $this->contextBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($contextMock);

        $this->customFilterApplierMock->expects($this->once())
            ->method('apply')
            ->with($contextMock, $customFilterMock, $selectMock)
            ->willThrowException(new \Exception($errorMessage));

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($errorMessage);

        $this->assertSame($selectMock, $this->plugin->afterBuild($searchIndexBuilderMock, $selectMock, $requestMock));
    }
}
