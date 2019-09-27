<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\Mode;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\Mode
 */
class ModeTest extends TestCase
{
    /**
     * @var Mode
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
            Mode::class,
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
                    'default_filter_mode' => '0',
                    'filter_mode' => 'single-select'
                ],
                'processedData' => [
                    'store_id' => '1',
                    'extension_attributes' => [
                        'filter_mode' => [
                            'filter_modes' => [
                                [
                                    'store_id' => '1',
                                    'value' => 'single-select'
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'default_filter_mode' => '1',
                    'filter_mode' => 'single-select',
                    'filter_modes' => [
                        0 => [
                            'store_id' => '1',
                            'value' => 'single-select'
                        ],
                        1 => [
                            'store_id' => '2',
                            'value' => 'multi-select'
                        ],
                    ]
                ],
                'processedData' => [
                    'store_id' => '1',
                    'extension_attributes' => [
                        'filter_mode' => [
                            'filter_modes' => [
                                1 => [
                                    'store_id' => '2',
                                    'value' => 'multi-select'
                                ],
                            ]
                        ]
                    ]
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'default_filter_mode' => '1',
                    'filter_mode' => 'single-select'
                ],
                'processedData' => [
                    'store_id' => '1',
                    'extension_attributes' => [
                        'filter_mode' => []
                    ]
                ]
            ],
        ];
    }
}
