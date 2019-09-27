<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\Filter\Item as FilterItem;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemFactory as FilterItemFactory;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder
 */
class ItemListBuilderTest extends TestCase
{
    /**
     * @var ItemListBuilder
     */
    private $model;

    /**
     * @var FilterItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterItemFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterItemFactoryMock = $this->createMock(FilterItemFactory::class);

        $this->model = $objectManager->getObject(
            ItemListBuilder::class,
            [
                'filterItemFactory' => $this->filterItemFactoryMock,
            ]
        );
    }

    /**
     * Test add method
     */
    public function testAdd()
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $label = 'label';
        $value = 145;
        $count = 20;
        $expectedItem = [
            'filter' => $filterMock,
            'label' => $label,
            'value' => $value,
            'count' => $count
        ];

        $this->assertEquals([], $this->getProperty('itemsData'));
        $this->assertSame($this->model, $this->model->add($filterMock, $label, $value, $count));
        $this->assertEquals([$expectedItem], $this->getProperty('itemsData'));
    }

    /**
     * Test create method
     */
    public function testCreate()
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $label = 'label';
        $value = 145;
        $count = 20;
        $item = [
            'filter' => $filterMock,
            'label' => $label,
            'value' => $value,
            'count' => $count
        ];
        $this->setProperty('itemsData', [$item]);

        $filterItemMock =  $this->createMock(FilterItem::class);
        $this->filterItemFactoryMock->expects($this->once())
            ->method('create')
            ->with($item)
            ->willReturn($filterItemMock);

        $this->assertEquals([$filterItemMock], $this->model->create());
        $this->assertEquals([], $this->getProperty('itemsData'));
    }

    /**
     * Get property
     *
     * @param string $propertyName
     * @return mixed
     * @throws \ReflectionException
     */
    private function getProperty($propertyName)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($this->model);
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
