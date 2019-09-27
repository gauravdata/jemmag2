<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\State;
use Aheadworks\Layerednav\Model\Layer\State\Item as StateItem;
use Aheadworks\Layerednav\Model\Layer\State\DefaultLayerState;
use Aheadworks\Layerednav\Model\Layer\State\ItemFactory as StateItemFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\State
 */
class StateTest extends TestCase
{
    /**
     * @var State
     */
    private $model;

    /**
     * @var StateItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stateItemFactoryMock;

    /**
     * @var DefaultLayerState|\PHPUnit_Framework_MockObject_MockObject
     */
    private $defaultLayerStateMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->stateItemFactoryMock = $this->createMock(StateItemFactory::class);
        $this->defaultLayerStateMock = $this->createMock(DefaultLayerState::class);

        $this->model = $objectManager->getObject(
            State::class,
            [
                'stateItemFactory' => $this->stateItemFactoryMock,
                'defaultLayerState' => $this->defaultLayerStateMock
            ]
        );
    }

    /**
     * Test addFilter method
     */
    public function testAddFilter()
    {
        $filterItemMock = $this->createMock(FilterItemInterface::class);
        $field = 'field';
        $condition = ['some-condition'];
        $orOption = true;

        $stateItemMock = $this->getStateItemMock($filterItemMock, $field, $condition, $orOption);
        $this->stateItemFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($stateItemMock);

        $this->defaultLayerStateMock->expects($this->once())
            ->method('addFilter')
            ->with($filterItemMock)
            ->willReturnSelf();

        $this->assertEquals([], $this->model->getItems());
        $this->assertSame($this->model, $this->model->addFilter($filterItemMock, $field, $condition, $orOption));
        $this->assertEquals([$stateItemMock], $this->model->getItems());
    }

    /**
     * Test getItems method
     *
     * @param $items
     * @dataProvider itemsDataProvider
     * @throws \ReflectionException
     */
    public function testGetItems($items)
    {
        $this->setProperty('items', $items);

        $this->assertEquals($items, $this->model->getItems());
    }

    /**
     * Test resetItems method
     *
     * @param $items
     * @dataProvider itemsDataProvider
     * @throws \ReflectionException
     */
    public function testResetItems($items)
    {
        $this->setProperty('items', $items);

        $this->assertSame($this->model, $this->model->resetItems());
        $this->assertEquals([], $this->model->getItems());
    }

    /**
     * @return array
     */
    public function itemsDataProvider()
    {
        return [
            [
                'items' => []
            ],
            [
                'items' => [$this->createMock(StateItem::class)]
            ],
            [
                'items' => [
                    $this->createMock(StateItem::class),
                    $this->createMock(StateItem::class)
                ]
            ],
        ];
    }

    /**
     * Get stateItemMock
     *
     * @param FilterItemInterface|\PHPUnit\Framework\MockObject\MockObject $filterItemMock
     * @param string $field
     * @param array $condition
     * @param bool $orOption
     * @return StateItem|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getStateItemMock($filterItemMock, $field, array $condition, $orOption)
    {
        $stateItemMock = $this->createMock(StateItem::class);
        $stateItemMock->expects($this->once())
            ->method('setFilterItem')
            ->with($filterItemMock)
            ->willReturnSelf();
        $stateItemMock->expects($this->once())
            ->method('setFilterField')
            ->with($field)
            ->willReturnSelf();
        $stateItemMock->expects($this->once())
            ->method('setFilterCondition')
            ->with($condition)
            ->willReturnSelf();
        $stateItemMock->expects($this->once())
            ->method('setFilterOrOption')
            ->with($orOption)
            ->willReturnSelf();

        return $stateItemMock;
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->model, $value);

        return $this;
    }
}
