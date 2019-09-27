<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\State\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Aheadworks\Layerednav\Model\Layer\State\Item\Resolver;
use Aheadworks\Layerednav\Model\Layer\State\Item as StateItem;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\State\Item\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * @var LayerState|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerStateMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->layerStateMock = $this->createMock(LayerState::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'layerState' => $this->layerStateMock,
            ]
        );
    }

    /**
     * Test getItemByFilter method
     *
     * @param string $filterCode
     * @param StateItem[] $items
     * @param StateItem|null $expectedResult
     * @dataProvider getItemByFilterDataProvider
     */
    public function testGetItemByFilter($filterCode, $items, $expectedResult)
    {
        $filterMock = $this->getFilterMock($filterCode);

        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->assertSame($expectedResult, $this->model->getItemByFilter($filterMock));
    }

    /**
     * @return array
     */
    public function getItemByFilterDataProvider()
    {
        $filterValidMock = $this->getFilterMock('test');
        $filterItemValidMock = $this->createMock(ItemInterface::class);
        $filterItemValidMock->expects($this->any())
            ->method('getFilter')
            ->willReturn($filterValidMock);

        $stateItemValidMock = $this->getStateItemMock($filterItemValidMock);

        $filterNotValidMock = $this->getFilterMock('not_valid');
        $filterItemNotValidMock = $this->createMock(ItemInterface::class);
        $filterItemNotValidMock->expects($this->any())
            ->method('getFilter')
            ->willReturn($filterNotValidMock);

        $stateItemNotValidMock = $this->getStateItemMock($filterItemNotValidMock);

        return [
            [
                'filterCode' => 'test',
                'items' => [$stateItemNotValidMock],
                'expectedResult' => null
            ],
            [
                'filterCode' => 'test',
                'items' => [$stateItemNotValidMock, $stateItemValidMock],
                'expectedResult' => $stateItemValidMock
            ],
            [
                'filterCode' => 'test',
                'items' => [$stateItemValidMock],
                'expectedResult' => $stateItemValidMock
            ],
        ];
    }

    /**
     * Get filter mock
     *
     * @param string $code
     * @return FilterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getFilterMock($code)
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getCode')
            ->willReturn($code);

        return $filterMock;
    }

    /**
     * Get state item mock
     *
     * @param FilterItemInterface|\PHPUnit\Framework\MockObject\MockObject $filterItemMock
     * @return StateItem|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getStateItemMock($filterItemMock)
    {
        $stateItemMock = $this->createMock(StateItem::class);
        $stateItemMock->expects($this->any())
            ->method('getFilterItem')
            ->willReturn($filterItemMock);

        return $stateItemMock;
    }
}
