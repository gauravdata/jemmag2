<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\NativeSwatches;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\NativeSwatches
 */
class NativeSwatchesTest extends TestCase
{
    /**
     * @var NativeSwatches
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
            NativeSwatches::class,
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
                    'extension_attributes' => [
                        'native_visual_swatches' => [],
                    ],
                ],
            ],
            [
                'postData' => [
                    'native_visual_swatches' => [
                        [
                            'swatch' => 'test1.png',
                        ],
                        [
                            'swatch' => '#dadada',
                        ],
                    ],
                ],
                'processedData' => [
                    'native_visual_swatches' => [
                        [
                            'swatch' => 'test1.png',
                        ],
                        [
                            'swatch' => '#dadada',
                        ],
                    ],
                    'extension_attributes' => [
                        'native_visual_swatches' => [
                            [
                                'swatch' => 'test1.png',
                                SwatchInterface::VALUE => 'test1.png',
                            ],
                            [
                                'swatch' => '#dadada',
                                SwatchInterface::VALUE => '#dadada',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'postData' => [
                    'native_visual_swatches' => [
                        [
                            'swatch' => 'test1.png',
                        ],
                        [
                            'swatch' => '#dadada',
                        ],
                        [
                            SwatchInterface::STOREFRONT_TITLES => [
                                1 => 'title 1',
                                2 => 'title 2',
                            ],
                        ],
                    ],
                ],
                'processedData' => [
                    'native_visual_swatches' => [
                        [
                            'swatch' => 'test1.png',
                        ],
                        [
                            'swatch' => '#dadada',
                        ],
                        [
                            SwatchInterface::STOREFRONT_TITLES => [
                                1 => 'title 1',
                                2 => 'title 2',
                            ],
                        ],
                    ],
                    'extension_attributes' => [
                        'native_visual_swatches' => [
                            [
                                'swatch' => 'test1.png',
                                SwatchInterface::VALUE => 'test1.png',
                            ],
                            [
                                'swatch' => '#dadada',
                                SwatchInterface::VALUE => '#dadada',
                            ],
                            [
                                SwatchInterface::STOREFRONT_TITLES => [
                                    [
                                        StoreValueInterface::STORE_ID => 1,
                                        StoreValueInterface::VALUE => 'title 1',
                                    ],
                                    [
                                        StoreValueInterface::STORE_ID => 2,
                                        StoreValueInterface::VALUE => 'title 2',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
