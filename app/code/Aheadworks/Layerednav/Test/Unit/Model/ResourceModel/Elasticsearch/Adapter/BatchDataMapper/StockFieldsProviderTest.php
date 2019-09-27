<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper;

use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper\StockFieldsProvider;
use Aheadworks\Layerednav\Model\Layer\Filter\Custom;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper\StockFieldsProvider
 */
class StockFieldsProviderTest extends TestCase
{
    /**
     * @var StockFieldsProvider
     */
    private $model;

    /**
     * @var StockRegistryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stockRegistryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->stockRegistryMock = $this->createMock(StockRegistryInterface::class);

        $this->model = $objectManager->getObject(
            StockFieldsProvider::class,
            [
                'stockRegistry' => $this->stockRegistryMock,
            ]
        );
    }

    /**
     * Test getFields method
     *
     * @param int[] $productIds
     * @param array $stockMap
     * @param array $expectedResult
     * @dataProvider getFieldsDataProvider
     */
    public function testGetFields($productIds, $stockMap, $expectedResult)
    {
        $storeId = 3;

        $this->stockRegistryMock->expects($this->any())
            ->method('getStockItem')
            ->will($this->returnValueMap($stockMap));

        $this->assertEquals($expectedResult, $this->model->getFields($productIds, $storeId));
    }

    /**
     * @return array
     */
    public function getFieldsDataProvider()
    {
        return [
            [
                'productIds' => [125, 126, 127],
                'stockMap' => [
                    [125, 3, $this->getStockItemMock(true)],
                    [126, 3, $this->getStockItemMock(false)],
                    [127, 3, $this->getStockItemMock(true)],
                ],
                'expectedResult' => [
                    125 => ['aw_stock' => FilterInterface::CUSTOM_FILTER_VALUE_YES],
                    126 => ['aw_stock' => FilterInterface::CUSTOM_FILTER_VALUE_NO],
                    127 => ['aw_stock' => FilterInterface::CUSTOM_FILTER_VALUE_YES]
                ]
            ],
            [
                'productIds' => [],
                'stockMap' => [
                    [125, 3, $this->getStockItemMock(true)],
                    [126, 3, $this->getStockItemMock(false)],
                    [127, 3, $this->getStockItemMock(true)],
                ],
                'expectedResult' => []
            ],
        ];
    }

    /**
     * Get stock item mock
     *
     * @param bool $isInStock
     * @return StockItemInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getStockItemMock($isInStock)
    {
        $stockItemMock = $this->createMock(StockItemInterface::class);
        $stockItemMock->expects($this->any())
            ->method('getIsInStock')
            ->willReturn($isInStock);

        return $stockItemMock;
    }
}
