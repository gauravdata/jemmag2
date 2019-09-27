<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\Checker;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as LayerFilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Aheadworks\Layerednav\Model\Layer\State\Item as StateItem;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\Checker
 */
class CheckerTest extends TestCase
{
    /**
     * @var Checker
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
            Checker::class,
            [
                'layerState' => $this->layerStateMock,
            ]
        );
    }

    /**
     * Test isActive when no active filter items found
     */
    public function testIsActiveNoActiveFilterItems()
    {
        $isActive = false;
        $activeFilterItems = [];

        $filterItem = $this->createMock(LayerFilterItemInterface::class);

        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($activeFilterItems);

        $this->assertEquals($isActive, $this->model->isActive($filterItem));
    }

    /**
     * Test isActive method
     */
    public function testIsActive()
    {
        $isActive = true;

        $filterItemValue = 'test value';
        $filterItemFilterCode = 'test filter code';

        $filterItemFilter = $this->createMock(FilterInterface::class);
        $filterItemFilter->expects($this->any())
            ->method('getCode')
            ->willReturn($filterItemFilterCode);

        $filterItem = $this->createMock(LayerFilterItemInterface::class);
        $filterItem->expects($this->any())
            ->method('getFilter')
            ->willReturn($filterItemFilter);
        $filterItem->expects($this->any())
            ->method('getValue')
            ->willReturn($filterItemValue);

        $firstActiveItem = $this->getStateItemMock('test code 1', 'test value 1');
        $secondActiveItem = $this->getStateItemMock('test filter code', 'test value 2');
        $thirdActiveItem = $this->getStateItemMock('test filter code', 'test value');
        $items = [
            $firstActiveItem,
            $secondActiveItem,
            $thirdActiveItem,
        ];

        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->assertEquals($isActive, $this->model->isActive($filterItem));
    }

    /**
     * Retrieve state item mock object
     *
     * @param string $filterCode
     * @param string $filterItemValue
     * @return \PHPUnit\Framework\MockObject\MockObject|StateItem
     */
    private function getStateItemMock($filterCode, $filterItemValue)
    {
        $stateItem = $this->createMock(StateItem::class);

        $filter = $this->createMock(FilterInterface::class);
        $filter->expects($this->any())
            ->method('getCode')
            ->willReturn($filterCode);

        $filterItem = $this->createMock(LayerFilterItemInterface::class);
        $filterItem->expects($this->any())
            ->method('getValue')
            ->willReturn($filterItemValue);
        $filterItem->expects($this->any())
            ->method('getFilter')
            ->willReturn($filter);

        $stateItem->expects($this->any())
            ->method('getFilterItem')
            ->willReturn($filterItem);

        return $stateItem;
    }
}
