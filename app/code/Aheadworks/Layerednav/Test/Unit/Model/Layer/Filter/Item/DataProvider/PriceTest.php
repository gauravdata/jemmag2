<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Price;
use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Range as RangePreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Price
 */
class PriceTest extends TestCase
{
    /**
     * @var Price
     */
    private $model;

    /**
     * @var FilterDataResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterDataResolverMock;

    /**
     * @var RangePreparer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rangePreparerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterDataResolverMock = $this->createMock(FilterDataResolver::class);
        $this->rangePreparerMock = $this->createMock(RangePreparer::class);

        $this->model = $objectManager->getObject(
            Price::class,
            [
                'filterDataResolver' => $this->filterDataResolverMock,
                'rangePreparer' => $this->rangePreparerMock,
            ]
        );
    }

    /**
     * Test getItemsData method
     *
     * @param array $facets
     * @param array $preparerMap
     * @param array $itemsData
     * @dataProvider getItemsDataDataProvider
     * @throws \ReflectionException
     */
    public function testGetItemsData($facets, $preparerMap, $itemsData)
    {
        $filterMock = $this->createMock(FilterInterface::class);

        $this->filterDataResolverMock->expects($this->once())
            ->method('getFacetedData')
            ->with($filterMock)
            ->willReturn($facets);

        if (empty($itemsData)) {
            $this->rangePreparerMock->expects($this->never())
                ->method('prepareData');
        } else {
            $this->rangePreparerMock->expects($this->any())
                ->method('prepareData')
                ->willReturnMap($preparerMap);
        }

        $this->assertEquals($itemsData, $this->model->getItemsData($filterMock));
    }

    /**
     * @return array
     */
    public function getItemsDataDataProvider()
    {
        $preparerMap = [
            ['*_20', 1, false, ['prepared-data-1']],
            ['10_20', 2, false, ['prepared-data-2']],
            ['20_*', 3, false, ['prepared-data-3']]
        ];

        return [
            [
                'facets' => [],
                'preparerMap' => $preparerMap,
                'itemsData' => []
            ],
            [
                'facets' => [
                    '10_20' => ['value' => '10_20', 'count' => 2]
                ],
                'preparerMap' => $preparerMap,
                'itemsData' => [
                    ['prepared-data-2']
                ]
            ],
            [
                'facets' => [
                    '*_20' => ['value' => '*_20', 'count' =>1],
                    '10_20' => ['value' => '10_20', 'count' => 2],
                    '20_*' => ['value' => '20_*', 'count' => 3]
                ],
                'preparerMap' => $preparerMap,
                'itemsData' => [
                    ['prepared-data-1'],
                    ['prepared-data-2'],
                    ['prepared-data-3']
                ]
            ],
            [
                'facets' => [
                    '*_20' => ['value' => '*_20', 'count' =>1],
                    '10_20' => ['value' => '10_20', 'count' => 2],
                    'bad' => ['value' => 'bad', 'count' => 3]
                ],
                'preparerMap' => $preparerMap,
                'itemsData' => [
                    ['prepared-data-1'],
                    ['prepared-data-2']
                ]
            ],
        ];
    }

    /**
     * Test getStatisticsData method
     *
     * @param array $facets
     * @param array $expectedResult
     * @dataProvider getStatisticsDataDataProvider
     * @throws \ReflectionException
     */
    public function testGetStatisticsData($facets, $expectedResult)
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $this->filterDataResolverMock->expects($this->once())
            ->method('getFacetedData')
            ->with($filterMock)
            ->willReturn($facets);

        $this->rangePreparerMock->expects($this->any())
            ->method('getFromValueByKey')
            ->willReturnMap(
                [
                    [
                        '0_20',
                        false,
                        0
                    ],
                    [
                        '10_20',
                        false,
                        10
                    ],
                    [
                        '10.5_20.95',
                        false,
                        10.5
                    ],
                    [
                        '*_20',
                        false,
                        0.0
                    ],
                    [
                        '10_*',
                        false,
                        10
                    ],
                ]
            );
        $this->rangePreparerMock->expects($this->any())
            ->method('getToValueByKey')
            ->willReturnMap(
                [
                    [
                        '0_20',
                        false,
                        20
                    ],
                    [
                        '10_20',
                        false,
                        20
                    ],
                    [
                        '10.5_20.95',
                        false,
                        20.95
                    ],
                    [
                        '*_20',
                        false,
                        20
                    ],
                    [
                        '10_*',
                        false,
                        ''
                    ],
                ]
            );

        $this->assertEquals($expectedResult, $this->model->getStatisticsData($filterMock));
    }

    /**
     * @return array
     */
    public function getStatisticsDataDataProvider()
    {
        return [
            [
                'facets' => [],
                'expectedResult' => []
            ],
            [
                'facets' => [
                    'stats' => [
                    ]
                ],
                'expectedResult' => []
            ],
            [
                'facets' => [
                    'stats' => [
                        'min' => 11,
                        'max' => 25
                    ]
                ],
                'expectedResult' => [
                    'minPrice' => 11,
                    'maxPrice' => 25,
                    'minSelectionPrice' => 11,
                    'maxSelectionPrice' => 25
                ]
            ],
            [
                'facets' => [
                    '0_20' => [
                        'value' => '0_20',
                        'count' => 4,
                    ],
                    'stats' => [
                        'min' => 11,
                        'max' => 25
                    ]
                ],
                'expectedResult' => [
                    'minPrice' => 11,
                    'maxPrice' => 25,
                    'minSelectionPrice' => 11,
                    'maxSelectionPrice' => 25,
                    'step' => 20,
                ]
            ],
            [
                'facets' => [
                    '10_20' => [
                        'value' => '10_20',
                        'count' => 5,
                    ],
                    'stats' => [
                        'min' => 11,
                        'max' => 25
                    ]
                ],
                'expectedResult' => [
                    'minPrice' => 11,
                    'maxPrice' => 25,
                    'minSelectionPrice' => 11,
                    'maxSelectionPrice' => 25,
                    'step' => 10,
                ]
            ],
            [
                'facets' => [
                    '10.5_20.95' => [
                        'value' => '10.5_20.95',
                        'count' => 5,
                    ],
                    'stats' => [
                        'min' => 11,
                        'max' => 25
                    ]
                ],
                'expectedResult' => [
                    'minPrice' => 11,
                    'maxPrice' => 25,
                    'minSelectionPrice' => 11,
                    'maxSelectionPrice' => 25,
                    'step' => 10.45,
                ]
            ],
            [
                'facets' => [
                    '*_20' => [
                        'value' => '*_20',
                        'count' => 5,
                    ],
                    'stats' => [
                        'min' => 11,
                        'max' => 25
                    ]
                ],
                'expectedResult' => [
                    'minPrice' => 11,
                    'maxPrice' => 25,
                    'minSelectionPrice' => 11,
                    'maxSelectionPrice' => 25,
                    'step' => 20,
                ]
            ],
            [
                'facets' => [
                    '10_*' => [
                        'value' => '10_*',
                        'count' => 5,
                    ],
                    'stats' => [
                        'min' => 11,
                        'max' => 25
                    ]
                ],
                'expectedResult' => [
                    'minPrice' => 11,
                    'maxPrice' => 25,
                    'minSelectionPrice' => 11,
                    'maxSelectionPrice' => 25,
                ]
            ],
        ];
    }
}
