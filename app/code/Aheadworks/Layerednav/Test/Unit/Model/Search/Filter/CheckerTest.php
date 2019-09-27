<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Filter;

use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Aheadworks\Layerednav\Model\Layer\State\Item as LayerStateItem;
use Aheadworks\Layerednav\Model\Search\Filter\Checker;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Filter\Checker
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
                'layerState' => $this->layerStateMock
            ]
        );
    }

    /**
     * Test hasAppliedFilters method
     *
     * @param LayerStateItem[]| $items
     * @param bool $expectedResult
     * @dataProvider hasAppliedFiltersDataProvider
     * @throws \ReflectionException
     */
    public function testHasAppliedFilters($items, $expectedResult)
    {
        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->assertEquals($expectedResult, $this->model->hasAppliedFilters());
    }

    /**
     * @return array
     */
    public function hasAppliedFiltersDataProvider()
    {
        return [
            [
                'items' => [],
                'expectedResult' => false
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('field1', []),
                ],
                'expectedResult' => true
            ],
            [   'items' => [
                    $this->getLayerStateItemMock('field1', []),
                    $this->getLayerStateItemMock('field2', []),
                ],
                'expectedResult' => true
            ],
        ];
    }

    /**
     * Test getAppliedFilters method
     *
     * @param array $items
     * @param string[] $expectedResult
     * @dataProvider getAppliedFiltersDataProvider
     */
    public function testGetAppliedFilters($items, $expectedResult)
    {
        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->assertEquals($expectedResult, $this->model->getAppliedFilters());
    }

    /**
     * @return array
     */
    public function getAppliedFiltersDataProvider()
    {
        return [
            [
                'items' => [],
                'expectedResult' => []
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('filter_extended_default', ['value1']),
                ],
                'expectedResult' => [
                    'filter_extended_default' => ['filter_extended_default']
                ]
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('filter_one', ['value1']),
                    $this->getLayerStateItemMock('filter_one', ['value1']),
                    $this->getLayerStateItemMock('filter_two', ['value2']),
                    $this->getLayerStateItemMock('filter-from-to', ['from' => 'value-from1', 'to' => 'value-to1']),
                    $this->getLayerStateItemMock('filter-from', ['from' => 'value-from2']),
                    $this->getLayerStateItemMock('filter-to', ['to' => 'value-to2']),
                ],
                'expectedResult' => [
                    'filter_one' => ['filter_one'] ,
                    'filter_two' => ['filter_two'],
                    'filter-from-to' => [
                        'filter-from-to.from',
                        'filter-from-to.to'
                    ],
                    'filter-from' => [
                        'filter-from.from',
                    ],
                    'filter-to' => [
                        'filter-to.to'
                    ]
                ]
            ],
        ];
    }

    /**
     * Test isApplied method
     *
     * @param array $items
     * @param string $code
     * @param bool $expectedResult
     * @dataProvider isAppliedDataProvider
     */
    public function testIsApplied($items, $code, $expectedResult)
    {
        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->assertEquals($expectedResult, $this->model->isApplied($code));
    }

    /**
     * @return array
     */
    public function isAppliedDataProvider()
    {
        return [
            [
                'items' => [],
                'code' => 'test',
                'expectedResult' => false
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('test', ['value']),
                ],
                'code' => 'test',
                'expectedResult' => true
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('test1', ['value1']),
                ],
                'code' => 'test',
                'expectedResult' => false
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('test-from-to', ['from' => 'from-value', 'to' => 'to-value']),
                ],
                'code' => 'test-from-to.from',
                'expectedResult' => true
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('test-from-to', ['from' => 'from-value', 'to' => 'to-value']),
                ],
                'code' => 'test-from-to',
                'expectedResult' => true
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('test-from-to', ['from' => 'from-value', 'to' => 'to-value']),
                ],
                'code' => 'test',
                'expectedResult' => false
            ],
        ];
    }

    /**
     * Test getExtendedFilters method
     *
     * @param array $items
     * @param string[] $expectedResult
     * @dataProvider getExtendedFiltersDataProvider
     */
    public function testGetExtendedFilters($items, $expectedResult)
    {
        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->assertEquals($expectedResult, $this->model->getExtendedFilters());
    }

    /**
     * @return array
     */
    public function getExtendedFiltersDataProvider()
    {
        return [
            [
                'items' => [],
                'expectedResult' => []
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('filter_default', ['value1'], false),
                    $this->getLayerStateItemMock('filter_extended_default', ['value2'], true),
                    $this->getLayerStateItemMock('filter_extended_default', ['value3'], true),
                ],
                'expectedResult' => [
                    'filter_extended_default' => ['filter_extended_default']
                ]
            ],
            [
                'items' => [
                    $this->getLayerStateItemMock('filter_extended', ['value1'], true),
                    $this->getLayerStateItemMock('filter_not_extended', ['value1'], false),
                    $this->getLayerStateItemMock(
                        'filter-from-to',
                        [
                            'from' => 'value-from1',
                            'to' => 'value-to1'
                        ],
                        true
                    ),
                    $this->getLayerStateItemMock('filter-from', ['from' => 'value-from2'], true),
                    $this->getLayerStateItemMock('filter-to', ['to' => 'value-to2'], true)
                ],
                'expectedResult' => [
                    'filter_extended' => ['filter_extended' ],
                    'filter-from-to' => [
                        'filter-from-to.from',
                        'filter-from-to.to'
                    ],
                    'filter-from' => [
                        'filter-from.from'
                    ],
                    'filter-to' => [
                        'filter-to.to'
                    ]
                ]
            ],
        ];
    }

    /**
     * Get layer state item mock
     *
     * @param string $field
     * @param array $condition
     * @param bool $orOption
     * @return LayerStateItem|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getLayerStateItemMock($field, array $condition, $orOption = false)
    {
        $layerStateItemMock = $this->createMock(LayerStateItem::class);
        $layerStateItemMock->expects($this->any())
            ->method('getFilterField')
            ->willReturn($field);
        $layerStateItemMock->expects($this->any())
            ->method('getFilterCondition')
            ->willReturn($condition);
        $layerStateItemMock->expects($this->any())
            ->method('getFilterOrOption')
            ->willReturn($orOption);

        return $layerStateItemMock;
    }
}
