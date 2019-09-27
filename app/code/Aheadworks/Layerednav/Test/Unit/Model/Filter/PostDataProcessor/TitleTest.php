<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\Title;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\Title
 */
class TitleTest extends TestCase
{
    /**
     * @var Title
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
            Title::class,
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
                    'title' => 'Test filter store',
                    'default_title' => 'Test filter',
                    'default_title_checkbox' => '0',
                ],
                'processedData' => [
                    'store_id' => '1',
                    'default_title' => 'Test filter',
                    'default_title_checkbox' => '0',
                    'title' => 'Test filter store',
                    'storefront_titles' => [
                        [
                            'store_id' => '1',
                            'value' => 'Test filter store'
                        ],
                    ],
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'title' => 'Test filter store',
                    'default_title' => 'Test filter',
                    'default_title_checkbox' => '1',
                ],
                'processedData' => [
                    'store_id' => '1',
                    'default_title' => 'Test filter',
                    'default_title_checkbox' => '1',
                    'title' => 'Test filter store',
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'title' => 'Test filter store',
                    'default_title' => 'Test filter',
                    'default_title_checkbox' => '1',
                    'storefront_titles' => [
                        0 => [
                            'store_id' => '1',
                            'value' => 'Test filter store 1'
                        ],
                        1 => [
                            'store_id' => '2',
                            'value' => 'Test filter store 2'
                        ],
                    ],
                ],
                'processedData' => [
                    'store_id' => '1',
                    'default_title' => 'Test filter',
                    'default_title_checkbox' => '1',
                    'title' => 'Test filter store',
                    'storefront_titles' => [
                        1 => [
                            'store_id' => '2',
                            'value' => 'Test filter store 2'
                        ],
                    ],
                ]
            ]
        ];
    }
}
