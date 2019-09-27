<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider\Preparer;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Range;
use Aheadworks\Layerednav\Model\Layer\Filter\Interval;
use Magento\Catalog\Model\Layer\Filter\Price\Render as PriceRenderer;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Range
 */
class RangeTest extends TestCase
{
    /**
     * @var Range
     */
    private $model;

    /**
     * @var PriceRenderer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceRendererMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->priceRendererMock = $this->createMock(PriceRenderer::class);

        $this->model = $objectManager->getObject(
            Range::class,
            [
                'priceRenderer' => $this->priceRendererMock
            ]
        );
    }

    /**
     * Test prepareData method
     *
     * @param string $key
     * @param int $count
     * @param Interval|false $selectedInterval
     * @param array $renderMap
     * @param array $expectedResult
     * @dataProvider prepareDataDataProvider
     */
    public function testPrepareData($key, $count, $selectedInterval, $renderMap, $expectedResult)
    {
        $this->priceRendererMock->expects($this->once())
            ->method('renderRangeLabel')
            ->willReturnMap($renderMap);

        $this->assertEquals($expectedResult, $this->model->prepareData($key, $count, $selectedInterval));
    }

    /**
     * @return array
     */
    public function prepareDataDataProvider()
    {
        $selectedIntervalMock = $this->createMock(Interval::class);
        $selectedIntervalMock->expects($this->any())
            ->method('getFrom')
            ->willReturn(5);
        $selectedIntervalMock->expects($this->any())
            ->method('getTo')
            ->willReturn(25);

        return [
            [
                'key' => '10_20',
                'count' => 5,
                'selectedInterval' => false,
                'renderMap' => [
                    [10, 20, '$10.00 - $19.99']
                ],
                'expectedResult' => [
                    'label' => '$10.00 - $19.99',
                    'value' => '10-20',
                    'count' => 5,
                    'from' => 10,
                    'to' => 20,
                ]
            ],
            [
                'key' => '10.5_20.95',
                'count' => 5,
                'selectedInterval' => false,
                'renderMap' => [
                    [10.5, 20.95, '$10.50 - $20.94']
                ],
                'expectedResult' => [
                    'label' => '$10.50 - $20.94',
                    'value' => '10.5-20.95',
                    'count' => 5,
                    'from' => 10.5,
                    'to' => 20.95,
                ]
            ],
            [
                'key' => '*_20',
                'count' => 6,
                'selectedInterval' => false,
                'renderMap' => [
                    [0, 20, '$0 - $19.99']
                ],
                'expectedResult' => [
                    'label' => '$0 - $19.99',
                    'value' => '0-20',
                    'count' => 6,
                    'from' => 0,
                    'to' => 20,
                ]
            ],
            [
                'key' => '10_*',
                'count' => 7,
                'selectedInterval' => false,
                'renderMap' => [
                    [10, '', '$10.00 and above']
                ],
                'expectedResult' => [
                    'label' => '$10.00 and above',
                    'value' => '10-',
                    'count' => 7,
                    'from' => 10,
                    'to' => '',
                ]
            ],
            [
                'key' => '*_20',
                'count' => 8,
                'selectedInterval' => $selectedIntervalMock,
                'renderMap' => [
                    [5, 20, '$5.00 - $19.99']
                ],
                'expectedResult' => [
                    'label' => '$5.00 - $19.99',
                    'value' => '5-20',
                    'count' => 8,
                    'from' => 5,
                    'to' => 20,
                ]
            ],
            [
                'key' => '10_*',
                'count' => 9,
                'selectedInterval' => $selectedIntervalMock,
                'renderMap' => [
                    [10, 25, '$5.00 - $24.99']
                ],
                'expectedResult' => [
                    'label' => '$5.00 - $24.99',
                    'value' => '10-25',
                    'count' => 9,
                    'from' => 10,
                    'to' => 25,
                ]
            ],
        ];
    }
}
