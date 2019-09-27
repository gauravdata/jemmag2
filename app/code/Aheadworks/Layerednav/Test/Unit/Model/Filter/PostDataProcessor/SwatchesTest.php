<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\Swatches;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\Swatches
 */
class SwatchesTest extends TestCase
{
    /**
     * @var Swatches
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
            Swatches::class,
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
                        'swatches' => [],
                    ],
                ],
            ],
            [
                'postData' => [
                    'swatches' => [
                        [
                            'swatch' => 'test1.png',
                        ],
                        [
                            'swatch' => '#dadada',
                        ],
                    ],
                ],
                'processedData' => [
                    'swatches' => [
                        [
                            'swatch' => 'test1.png',
                        ],
                        [
                            'swatch' => '#dadada',
                        ],
                    ],
                    'extension_attributes' => [
                        'swatches' => [
                            [
                                'swatch' => 'test1.png',
                                SwatchInterface::VALUE => null,
                                SwatchInterface::IMAGE => [
                                    ImageInterface::FILE_NAME => 'test1.png',
                                ],
                            ],
                            [
                                'swatch' => '#dadada',
                                SwatchInterface::VALUE => '#dadada',
                                SwatchInterface::IMAGE => null,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'postData' => [
                    'swatches' => [
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
                    'swatches' => [
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
                        'swatches' => [
                            [
                                'swatch' => 'test1.png',
                                SwatchInterface::VALUE => null,
                                SwatchInterface::IMAGE => [
                                    ImageInterface::FILE_NAME => 'test1.png',
                                ],
                            ],
                            [
                                'swatch' => '#dadada',
                                SwatchInterface::VALUE => '#dadada',
                                SwatchInterface::IMAGE => null,
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
