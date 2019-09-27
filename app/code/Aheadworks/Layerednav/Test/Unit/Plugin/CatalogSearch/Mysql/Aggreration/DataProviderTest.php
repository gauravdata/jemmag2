<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin\CatalogSearch\Mysql\Aggregation;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\AggregationProviderInterface;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\CustomDataProviderPool;
use Aheadworks\Layerednav\Plugin\CatalogSearch\Mysql\Aggregation\DataProvider;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Search\Request\DimensionFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\Dimension;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider as AggregationDataProvider;

/**
 * Test for \Aheadworks\Layerednav\Plugin\CatalogSearch\Mysql\Aggregation\DataProvider
 */
class DataProviderTest extends TestCase
{
    /**
     * @var DataProvider
     */
    private $plugin;

    /**
     * @var CustomDataProviderPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customDataProviderPoolMock;

    /**
     * @var HttpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContextMock;

    /**
     * @var DimensionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dimensionFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->customDataProviderPoolMock = $this->createMock(CustomDataProviderPool::class);
        $this->httpContextMock = $this->createMock(HttpContext::class);
        $this->dimensionFactoryMock = $this->createMock(DimensionFactory::class);

        $this->plugin = $objectManager->getObject(
            DataProvider::class,
            [
                'customDataProviderPool' => $this->customDataProviderPoolMock,
                'httpContext' => $this->httpContextMock,
                'dimensionFactory' => $this->dimensionFactoryMock
            ]
        );
    }

    /**
     * Test aroundGetDataSet method
     */
    public function testAroundGetDataSet()
    {
        $customerGroupId = 123;
        $field = 'field';
        $subjectMock = $this->createMock(AggregationDataProvider::class);
        $bucketMock = $this->getBucketMock($field);
        $tableMock = $this->createMock(Table::class);

        $selectMock = $this->createMock(Select::class);
        $isProceedCalled = false;
        $proceed = function ($bucketMock, $dimensions, $tableMock) use (&$isProceedCalled, $selectMock) {
            $isProceedCalled = true;
            return $selectMock;
        };

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(Context::CONTEXT_GROUP)
            ->willReturn($customerGroupId);
        $dimensionMock = $this->createMock(Dimension::class);
        $this->dimensionFactoryMock->expects($this->once())
            ->method('create')
            ->with([
                'name' => Context::CONTEXT_GROUP,
                'value' => $customerGroupId
            ])
            ->willReturn($dimensionMock);
        $dimensions = [
            Context::CONTEXT_GROUP => $dimensionMock
        ];

        $aggregationDataProviderMock = $this->getAggregationDataProviderMock($dimensions, $tableMock, $selectMock);
        $this->customDataProviderPoolMock->expects($this->once())
            ->method('getAggregationProvider')
            ->with($field)
            ->willReturn($aggregationDataProviderMock);

        $this->assertSame(
            $selectMock,
            $this->plugin->aroundGetDataSet(
                $subjectMock,
                $proceed,
                $bucketMock,
                $dimensions,
                $tableMock
            )
        );
        $this->assertFalse($isProceedCalled);
    }

    /**
     * Test aroundGetDataSet method if no aggregation data provider
     */
    public function testAroundGetDataSetNoAggregationDataProvider()
    {
        $field = 'field';
        $subjectMock = $this->createMock(AggregationDataProvider::class);
        $bucketMock = $this->getBucketMock($field);
        $dimensions = [];
        $tableMock = $this->createMock(Table::class);

        $selectMock = $this->createMock(Select::class);
        $isProceedCalled = false;
        $proceed = function ($bucketMock, $dimensions, $tableMock) use (&$isProceedCalled, $selectMock) {
            $isProceedCalled = true;
            return $selectMock;
        };

        $this->customDataProviderPoolMock->expects($this->once())
            ->method('getAggregationProvider')
            ->with($field)
            ->willReturn(null);

        $this->httpContextMock->expects($this->never())
            ->method('getValue');

        $this->assertSame(
            $selectMock,
            $this->plugin->aroundGetDataSet(
                $subjectMock,
                $proceed,
                $bucketMock,
                $dimensions,
                $tableMock
            )
        );
        $this->assertTrue($isProceedCalled);
    }

    /**
     * Get bucket mock
     *
     * @param string $field
     * @return BucketInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getBucketMock($field)
    {
        $bucketMock = $this->createMock(BucketInterface::class);
        $bucketMock->expects($this->any())
            ->method('getField')
            ->willReturn($field);

        return $bucketMock;
    }

    /**
     * Get aggregation data provider
     *
     * @param Dimension[]|\PHPUnit\Framework\MockObject\MockObject[] $dimensions
     * @param Table|\PHPUnit\Framework\MockObject\MockObject $tableMock
     * @param Select|\PHPUnit\Framework\MockObject\MockObject $selectMock
     * @return AggregationProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getAggregationDataProviderMock(array $dimensions, $tableMock, $selectMock)
    {
        $aggregationDataProvider = $this->createMock(AggregationProviderInterface::class);
        $aggregationDataProvider->expects($this->once())
            ->method('getDataSet')
            ->with($dimensions, $tableMock)
            ->willReturn($selectMock);

        return $aggregationDataProvider;
    }
}
