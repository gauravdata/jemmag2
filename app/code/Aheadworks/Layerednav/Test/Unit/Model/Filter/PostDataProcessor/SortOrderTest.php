<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\SortOrder;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\SortOrder
 */
class SortOrderTest extends TestCase
{
    /**
     * @var SortOrder
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(
            SortOrder::class,
            []
        );
    }

    /**
     * Test process method
     *
     * @param $postData
     * @param $processedData
     * @dataProvider processDataProvider
     */
    public function testProcess($postData, $processedData)
    {
        $this->assertEquals($processedData, $this->model->process($postData));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'postData' => [
                    'store_id' => '1',
                    'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
                    'default_sort_order' => '0',
                ],
                'processedData' => [
                    'store_id' => '1',
                    'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
                    'default_sort_order' => '0',
                    'sort_orders' => [
                        [
                            'store_id' => '1',
                            'value' => FilterInterface::SORT_ORDER_MANUAL
                        ]
                    ],
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
                    'default_sort_order' => '1',
                ],
                'processedData' => [
                    'store_id' => '1',
                    'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
                    'default_sort_order' => '1',
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
                    'default_sort_order' => '1',
                    'sort_orders' => [
                        0 => [
                            'store_id' => '1',
                            'value' => FilterInterface::SORT_ORDER_MANUAL
                        ],
                        1 => [
                            'store_id' => '2',
                            'value' => FilterInterface::SORT_ORDER_DESC
                        ],
                    ],
                ],
                'processedData' => [
                    'store_id' => '1',
                    'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
                    'default_sort_order' => '1',
                    'sort_orders' => [
                        1 => [
                            'store_id' => '2',
                            'value' => FilterInterface::SORT_ORDER_DESC
                        ],
                    ],
                ]
            ]
        ];
    }
}
