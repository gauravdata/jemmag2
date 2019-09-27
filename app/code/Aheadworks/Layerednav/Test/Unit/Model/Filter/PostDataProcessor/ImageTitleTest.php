<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\ImageTitle;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\ImageTitle
 */
class ImageTitleTest extends TestCase
{
    /**
     * @var ImageTitle
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
            ImageTitle::class,
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
                'postData' => [],
                'processedData' => [],
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'image_title' => 'Test image title',
                    'default_image_title' => '0',
                ],
                'processedData' => [
                    'store_id' => '1',
                    'image_title' => 'Test image title',
                    'default_image_title' => '0',
                    'image_titles' => [
                        [
                            'store_id' => '1',
                            'value' => 'Test image title',
                        ],
                    ],
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'image_title' => 'Test image title',
                    'default_image_title' => '1',
                ],
                'processedData' => [
                    'store_id' => '1',
                    'image_title' => 'Test image title',
                    'default_image_title' => '1',
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'image_title' => 'Test image title',
                    'default_image_title' => '1',
                    'image_titles' => [
                        0 => [
                            'store_id' => '1',
                            'value' => 'Test image title store 1'
                        ],
                        1 => [
                            'store_id' => '2',
                            'value' => 'Test image title store 2'
                        ],
                    ],
                ],
                'processedData' => [
                    'store_id' => '1',
                    'image_title' => 'Test image title',
                    'default_image_title' => '1',
                    'image_titles' => [
                        1 => [
                            'store_id' => '2',
                            'value' => 'Test image title store 2'
                        ],
                    ],
                ]
            ]
        ];
    }
}
