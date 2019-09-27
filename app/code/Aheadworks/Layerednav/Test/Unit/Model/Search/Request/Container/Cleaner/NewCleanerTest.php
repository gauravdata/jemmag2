<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Request\Container\Cleaner;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner\NewCleaner;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Request\Container\Cleaner\NewCleaner
 */
class NewCleanerTest extends TestCase
{
    /**
     * @var NewCleaner
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);

        $this->model = $objectManager->getObject(
            NewCleaner::class,
            [
                'config' => $this->configMock,
            ]
        );
    }

    /**
     * Test perform method
     *
     * @param $data
     * @param $filter
     * @param $isNewFilterEnabled
     * @param $expectedResult
     * @dataProvider performDataProvider
     */
    public function testPerform($data, $filter, $isNewFilterEnabled, $expectedResult)
    {
        $this->configMock->expects($this->once())
            ->method('isNewFilterEnabled')
            ->willReturn($isNewFilterEnabled);

        $this->assertEquals($expectedResult, $this->model->perform($data, $filter));
    }

    /**
     * @return array
     */
    public function performDataProvider()
    {
        return [
            [
                'data' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one_query'],
                                ['ref' => 'test_two_query']
                            ]
                        ],
                        'test_one_query' => [],
                        'test_two_query' => [],
                    ],
                    'filters' => [
                        'test_one_filter' => [],
                        'test_two_filter' => [],
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                        'test_two_bucket' => [],
                    ],
                    'query' => 'container_name'
                ],
                'filter' => 'test_two',
                'isNewFilterEnabled' => false,
                'expectedResult' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one_query']
                            ]
                        ],
                        'test_one_query' => [],
                    ],
                    'filters' => [
                        'test_one_filter' => [],
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                    ],
                    'query' => 'container_name'
                ]
            ],
            [
                'data' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one_query'],
                                ['ref' => 'test_two_query']
                            ]
                        ],
                        'test_one_query' => [],
                        'test_two_query' => [],
                    ],
                    'filters' => [
                        'test_one_filter' => [],
                        'test_two_filter' => [],
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                        'test_two_bucket' => [],
                    ],
                    'query' => 'container_name'
                ],
                'filter' => 'test',
                'isNewFilterEnabled' => false,
                'expectedResult' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one_query'],
                                ['ref' => 'test_two_query']
                            ]
                        ],
                        'test_one_query' => [],
                        'test_two_query' => [],
                    ],
                    'filters' => [
                        'test_one_filter' => [],
                        'test_two_filter' => [],
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                        'test_two_bucket' => [],
                    ],
                    'query' => 'container_name'
                ]
            ],
            [
                'data' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one_query'],
                                ['ref' => 'test_two_query']
                            ],
                            'test_one_query' => [],
                            'test_two_query' => [],
                        ]
                    ],
                    'filters' => [
                        'test_one_filter' => [],
                        'test_two_filter' => [],
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                        'test_two_bucket' => [],
                    ],
                    'query' => 'container_name'
                ],
                'filter' => 'test_two',
                'isNewFilterEnabled' => true,
                'expectedResult' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one_query'],
                                ['ref' => 'test_two_query']
                            ],
                            'test_one_query' => [],
                            'test_two_query' => [],
                        ]
                    ],
                    'filters' => [
                        'test_one_filter' => [],
                        'test_two_filter' => [],
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                        'test_two_bucket' => [],
                    ],
                    'query' => 'container_name'
                ]
            ],
        ];
    }

    /**
     * Test perform method if not valid data specified
     *
     * @param $data
     * @param $filter
     * @dataProvider performErrorDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Bad container data specified!
     */
    public function testPerformError($data, $filter)
    {
        $this->configMock->expects($this->once())
            ->method('isNewFilterEnabled')
            ->willReturn(false);

        $this->model->perform($data, $filter);
    }

    /**
     * @return array
     */
    public function performErrorDataProvider()
    {
        return [
            [
                'data' => [],
                'filter' => 'test'
            ],
            [
                'data' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one'],
                            ]
                        ]
                    ],
                    'filters' => [
                        'test_one_filter' => [],
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                      ],
                ],
                'filter' => 'test'
            ],
            [
                'data' => [
                    'filters' => [
                        'test_one_filter' => [],
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                    ],
                    'query' => 'container_name'
                ],
                'filter' => 'test'
            ],
            [
                'data' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one'],
                            ]
                        ]
                    ],
                    'aggregations' => [
                        'test_one_bucket' => [],
                    ],
                    'query' => 'container_name'
                ],
                'filter' => 'test'
            ],
            [
                'data' => [
                    'queries' => [
                        'container_name' => [
                            'queryReference' => [
                                ['ref' => 'test_one'],
                            ]
                        ]
                    ],
                    'filters' => [
                        'test_one_filter' => [],
                    ],
                    'query' => 'container_name'
                ],
                'filter' => 'test'
            ],
        ];
    }
}
