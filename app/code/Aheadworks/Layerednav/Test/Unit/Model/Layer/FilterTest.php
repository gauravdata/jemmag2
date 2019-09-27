<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\Filter;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderInterface as ItemsProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter
 */
class FilterTest extends TestCase
{
    /**
     * @var Filter
     */
    private $model;

    /**
     * @var ItemsProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemsProviderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->itemsProviderMock = $this->createMock(ItemsProviderInterface::class);

        $this->model = $objectManager->getObject(
            Filter::class,
            [
                'itemsProvider' => $this->itemsProviderMock,
            ]
        );
    }

    /**
     * Test getItems method
     */
    public function testGetItems()
    {
        $filterItemOneMock = $this->createMock(FilterItemInterface::class);
        $filterItemTwoMock = $this->createMock(FilterItemInterface::class);
        $items = [$filterItemOneMock, $filterItemTwoMock];

        $this->itemsProviderMock->expects($this->once())
            ->method('getItems')
            ->with($this->model)
            ->willReturn($items);

        $this->assertEquals($items, $this->model->getItems());
    }

    /**
     * Test getItemsCount method
     *
     * @param $items
     * @param $expectedResult
     * @dataProvider getItemsCountDataProvider
     */
    public function testGetItemsCount($items, $expectedResult)
    {
        $this->itemsProviderMock->expects($this->once())
            ->method('getItems')
            ->with($this->model)
            ->willReturn($items);

        $this->assertEquals($expectedResult, $this->model->getItemsCount());
    }

    /**
     * @return array
     */
    public function getItemsCountDataProvider()
    {
        $filterItemOneMock = $this->createMock(FilterItemInterface::class);
        $filterItemTwoMock = $this->createMock(FilterItemInterface::class);

        return [
            [
                'items' => [],
                'expectedResult' => 0
            ],
            [
                'items' => [$filterItemOneMock],
                'expectedResult' => 1
            ],
            [
                'items' => [$filterItemOneMock, $filterItemTwoMock],
                'expectedResult' => 2
            ],
        ];
    }

    /**
     * Test getAdditionalData method
     */
    public function testGetAdditionalData()
    {
        $this->model->setData(Filter::ADDITIONAL_DATA, ['test' => 'test-data']);

        $this->assertEquals('test-data', $this->model->getAdditionalData('test'));
    }

    /**
     * Test getAdditionalData method if no data found
     */
    public function testGetAdditionalDataNoData()
    {
        $this->model->setData(Filter::ADDITIONAL_DATA, []);

        $this->assertNull($this->model->getAdditionalData('no_data'));
    }
}
