<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\State;

use Aheadworks\Layerednav\Model\Layer\State\DefaultFilter;
use Aheadworks\Layerednav\Model\Layer\State\DefaultLayerState;
use Aheadworks\Layerednav\Model\Layer\State\DefaultFilterFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\State;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\State\DefaultLayerState
 */
class DefaultLayerStateTest extends TestCase
{
    /**
     * @var DefaultLayerState
     */
    private $model;

    /**
     * @var DefaultFilterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $defaultFilterFactoryMock;

    /**
     * @var ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $defaultItemFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->defaultFilterFactoryMock = $this->createMock(DefaultFilterFactory::class);
        $this->defaultItemFactoryMock = $this->createMock(ItemFactory::class);

        $this->model = $objectManager->getObject(
            DefaultLayerState::class,
            [
                'defaultFilterFactory' => $this->defaultFilterFactoryMock,
                'defaultItemFactory' => $this->defaultItemFactoryMock,
            ]
        );
    }

    /**
     * Test addFilter method
     */
    public function testAddFilter()
    {
        $filterCode = 'test-code';
        $label = 'Test Label';
        $value = '125';
        $count = '11';

        $layerMock = $this->createMock(Layer::class);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($filterCode);
        $filterMock->expects($this->once())
            ->method('getLayer')
            ->willReturn($layerMock);

        $filterItemMock = $this->getFilterItemMock($filterMock, $label, $value, $count);

        $defaultFilterMock = $this->createMock(DefaultFilter::class);
        $defaultFilterMock->expects($this->once())
            ->method('setRequestVar')
            ->with($filterCode)
            ->willReturnSelf();
        $this->defaultFilterFactoryMock->expects($this->once())
            ->method('create')
            ->with(['layer' => $layerMock])
            ->willReturn($defaultFilterMock);

        $defaultFilterItemMock = $this->getDefaultFilterItemMock($defaultFilterMock, $label, $value, $count);
        $this->defaultItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($defaultFilterItemMock);

        $defaultLayerStateMock = $this->createMock(State::class);
        $defaultLayerStateMock->expects($this->once())
            ->method('addFilter')
            ->with($defaultFilterItemMock);

        $layerMock->expects($this->once())
            ->method('getState')
            ->willReturn($defaultLayerStateMock);

        $this->assertNull($this->model->addFilter($filterItemMock));
    }

    /**
     * @param FilterInterface|\PHPUnit\Framework\MockObject\MockObject $filterMock
     * @param string $label
     * @param string $value
     * @param string|int $count
     * @return FilterItemInterface| \PHPUnit\Framework\MockObject\MockObject
     */
    private function getFilterItemMock($filterMock, $label, $value, $count)
    {
        $filterItemMock = $this->createMock(FilterItemInterface::class);
        $filterItemMock->expects($this->once())
            ->method('getFilter')
            ->willReturn($filterMock);
        $filterItemMock->expects($this->once())
            ->method('getLabel')
            ->willReturn($label);
        $filterItemMock->expects($this->once())
            ->method('getValue')
            ->willReturn($value);
        $filterItemMock->expects($this->once())
            ->method('getCount')
            ->willReturn($count);

        return $filterItemMock;
    }

    /**
     * @param DefaultFilter|\PHPUnit\Framework\MockObject\MockObject $defaultFilterMock
     * @param string $label
     * @param string $value
     * @param string|int $count
     * @return Item|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getDefaultFilterItemMock($defaultFilterMock, $label, $value, $count)
    {
        $defaultFilterItemMock = $this->createPartialMock(
            Item::class,
            ['setFilter', 'setLabel', 'setValue', 'setCount']
        );
        $defaultFilterItemMock->expects($this->once())
            ->method('setFilter')
            ->with($defaultFilterMock)
            ->willReturnSelf();
        $defaultFilterItemMock->expects($this->once())
            ->method('setLabel')
            ->with($label)
            ->willReturnSelf();
        $defaultFilterItemMock->expects($this->once())
            ->method('setValue')
            ->with($value)
            ->willReturnSelf();
        $defaultFilterItemMock->expects($this->once())
            ->method('setCount')
            ->with($count)
            ->willReturnSelf();

        return $defaultFilterItemMock;
    }
}
