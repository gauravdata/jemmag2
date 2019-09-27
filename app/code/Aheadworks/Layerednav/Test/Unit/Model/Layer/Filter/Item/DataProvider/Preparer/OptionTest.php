<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider\Preparer;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Option;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Preparer\Option
 */
class OptionTest extends TestCase
{
    /**
     * @var Option
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

        $this->model = $objectManager->getObject(Option::class, []);
    }

    /**
     * Test perform method
     *
     * @param array $options
     * @param array $counts
     * @param bool $withCountsOnly
     * @param array $expectedResult
     * @dataProvider performDataProvider
     */
    public function testPerform($options, $counts, $withCountsOnly, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->model->perform($options, $counts, $withCountsOnly));
    }

    /**
     * @return array
     */
    public function performDataProvider()
    {
        return [
            [
                'options' => [],
                'counts' => [],
                'withCountsOnly' => true,
                'expectedResult' => []
            ],
            [
                'options' => [],
                'counts' => [],
                'withCountsOnly' => false,
                'expectedResult' => []
            ],
            [
                'options' => [
                    ['value' => 11, 'label' => 'label-11'],
                    ['value' => 12, 'label' => 'label-12'],
                    ['value' => 13, 'label' => 'label-13'],
                    ['value' => 14, 'label' => 'label-14'],
                    ['value' => [15, 16], 'label' => 'label-bad'],
                ],
                'counts' => [
                    12 => ['value' => 12, 'count' => 15],
                    13 => ['value' => 13, 'count' => 0],
                    11 => ['value' => 11, 'count' => 5],
                ],
                'withCountsOnly' => true,
                'expectedResult' => [
                    ['value' => 11, 'count' => 5, 'label' => 'label-11'],
                    ['value' => 12, 'count' => 15, 'label' => 'label-12'],
                    ['value' => 13, 'count' => 0, 'label' => 'label-13'],
                ]
            ],
            [
                'options' => [
                    ['value' => 11, 'label' => 'label-11'],
                    ['value' => 12, 'label' => 'label-12'],
                    ['value' => 13, 'label' => 'label-13'],
                    ['value' => 14, 'label' => 'label-14'],
                    ['value' => [15, 16], 'label' => 'label-bad'],
                ],
                'counts' => [
                    12 => ['value' => 12, 'count' => 15],
                    13 => ['value' => 13, 'count' => 0],
                    11 => ['value' => 11, 'count' => 5],
                ],
                'withCountsOnly' => false,
                'expectedResult' => [
                    ['value' => 11, 'count' => 5, 'label' => 'label-11'],
                    ['value' => 12, 'count' => 15, 'label' => 'label-12'],
                    ['value' => 13, 'count' => 0, 'label' => 'label-13'],
                    ['value' => 14, 'count' => 0, 'label' => 'label-14'],
                ]
            ],
        ];
    }
}
