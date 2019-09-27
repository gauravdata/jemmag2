<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\Image;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\Image
 */
class ImageTest extends TestCase
{
    /**
     * @var Image
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
            Image::class,
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
                'processedData' => [
                    'image' => null
                ]
            ],
            [
                'postData' => [
                    'image' => null
                ],
                'processedData' => [
                    'image' => null
                ]
            ],
            [
                'postData' => [
                    'image' => []
                ],
                'processedData' => [
                    'image' => null
                ]
            ],
            [
                'postData' => [
                    'image' => [
                        ['test.jpg']
                    ]
                ],
                'processedData' => [
                    'image' => ['test.jpg']
                ]
            ],
        ];
    }
}
