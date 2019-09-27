<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\Provider;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemFactory as FilterItemFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\Provider
 */
class ProviderTest extends TestCase
{
    /**
     * @var Provider
     */
    private $model;

    /**
     * @var FilterItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterItemFactoryMock;

    /**
     * @var DataProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProviderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterItemFactoryMock = $this->createMock(FilterItemFactory::class);
        $this->dataProviderMock = $this->createMock(DataProviderInterface::class);

        $this->model = $objectManager->getObject(
            Provider::class,
            [
                'filterItemFactory' => $this->filterItemFactoryMock,
                'dataProvider' => $this->dataProviderMock
            ]
        );
    }

    /**
     * Test getItems method
     */
    public function testGetItems()
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $itemsData = [
            ['label' => 'Test 1', 'value' => 11, 'count' => 25, 'imageData' => ['image-data']],
            ['label' => 'Test 2', 'value' => 22, 'count' => 0],
        ];

        $this->dataProviderMock->expects($this->once())
            ->method('getItemsData')
            ->with($filterMock)
            ->willReturn($itemsData);

        $filterItemOneMock = $this->createMock(FilterItemInterface::class);
        $filterItemTwoMock = $this->createMock(FilterItemInterface::class);
        $items = [$filterItemOneMock, $filterItemTwoMock];

        $map = [
            [
                [
                    'filter'=> $filterMock,
                    'label' => 'Test 1',
                    'value' => 11, 'count' => 25,
                    'imageData' => ['image-data'],
                ],
                $filterItemOneMock
            ],
            [
                [
                    'filter'=> $filterMock,
                    'label' => 'Test 2',
                    'value' => 22,
                    'count' => 0,
                    'imageData' => [],
                ],
                $filterItemTwoMock
            ],
        ];
        $this->filterItemFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturnMap($map);

        $this->assertEquals($items, $this->model->getItems($filterMock));
    }

    /**
     * Test getStatisticsData method
     */
    public function testGetStatisticsData()
    {
        $data = ['statistics-data'];

        $filterMock = $this->createMock(FilterInterface::class);

        $this->dataProviderMock->expects($this->once())
            ->method('getStatisticsData')
            ->willReturn($data);

        $this->assertEquals($data, $this->model->getStatisticsData($filterMock));
    }
}
