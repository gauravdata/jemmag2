<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider\Preparer;

use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Swatch;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

use Aheadworks\Layerednav\Model\Filter\Swatch\Finder as SwatchFinder;
use Aheadworks\Layerednav\Model\Image\DataConverter as ImageDataConverter;
use Aheadworks\Layerednav\Model\Image\Resolver as ImageResolver;
use Magento\Framework\Exception\LocalizedException;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Swatch
 */
class SwatchTest extends TestCase
{
    /**
     * @var Swatch
     */
    private $model;

    /**
     * @var SwatchFinder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $swatchFinderMock;

    /**
     * @var ImageDataConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageDataConverterMock;

    /**
     * @var ImageResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->swatchFinderMock = $this->createMock(SwatchFinder::class);
        $this->imageDataConverterMock = $this->createMock(ImageDataConverter::class);
        $this->imageResolverMock = $this->createMock(ImageResolver::class);

        $this->model = $objectManager->getObject(
            Swatch::class,
            [
                'swatchFinder' => $this->swatchFinderMock,
                'imageDataConverter' => $this->imageDataConverterMock,
                'imageResolver' => $this->imageResolverMock,
            ]
        );
    }

    /**
     * Test perform method when options are specified incorrectly
     *
     * @param array $options
     * @dataProvider performIncorrectOptionsDataProvider
     */
    public function testPerformIncorrectOptions($options)
    {
        $result = [];

        $this->assertEquals($result, $this->model->perform($options));
    }

    /**
     * @return array
     */
    public function performIncorrectOptionsDataProvider()
    {
        return [
            [
                'options' => [],
            ],
            [
                'options' => [
                    [
                        'value' => '',
                    ]
                ],
            ],
            [
                'options' => [
                    [
                        'value' => null,
                    ]
                ],
            ],
            [
                'options' => [
                    [
                        'value' => [],
                    ]
                ],
            ],
        ];
    }

    /**
     * Test preform method
     */
    public function testPerform()
    {
        $firstValue = 'test first value';
        $secondValue = 'test second value';
        $thirdValue = 'test third value';
        $thirdValueColor = '#dadada';
        $options = [
            [
                'value' => $firstValue,
            ],
            [
                'value' => $secondValue,
            ],
            [
                'value' => $thirdValue,
            ],
        ];

        $secondValueSwatchItem = $this->createMock(SwatchInterface::class);
        $secondValueSwatchItem->expects($this->any())
            ->method('getImage')
            ->willReturn(null);
        $secondValueSwatchItem->expects($this->any())
            ->method('getValue')
            ->willReturn(null);

        $thirdValueSwatchItem = $this->createMock(SwatchInterface::class);
        $thirdValueSwatchItem->expects($this->any())
            ->method('getImage')
            ->willReturn(null);
        $thirdValueSwatchItem->expects($this->any())
            ->method('getValue')
            ->willReturn($thirdValueColor);

        $this->swatchFinderMock->expects($this->exactly(3))
            ->method('getByOptionId')
            ->willReturnMap(
                [
                    [
                        $firstValue,
                        null,
                    ],
                    [
                        $secondValue,
                        $secondValueSwatchItem,
                    ],
                    [
                        $thirdValue,
                        $thirdValueSwatchItem,
                    ],
                ]
            );

        $result = [
            [
                'value' => $firstValue,
                'image' => [],
            ],
            [
                'value' => $secondValue,
                'image' => [],
            ],
            [
                'value' => $thirdValue,
                'image' => [
                    'color' => $thirdValueColor,
                ],
            ],
        ];

        $this->assertEquals($result, $this->model->perform($options));
    }
}
