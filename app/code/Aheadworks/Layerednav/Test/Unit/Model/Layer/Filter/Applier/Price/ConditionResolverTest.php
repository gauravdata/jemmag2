<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Applier\Price;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Price\ConditionResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Applier\Price\ConditionResolver
 */
class ConditionResolverTest extends TestCase
{
    /**
     * @var ConditionResolver
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);

        $this->model = $objectManager->getObject(
            ConditionResolver::class,
            [
                'config' => $this->configMock,
            ]
        );
    }

    /**
     * Test getFromToCondition method
     *
     * @param int|float|string $from
     * @param int|float|string $to
     * @param bool $isManualFromToPriceFilterEnabled
     * @param array $expectedResult
     * @dataProvider getFromToConditionDataProvider
     */
    public function testGetFromToCondition($from, $to, $isManualFromToPriceFilterEnabled, $expectedResult)
    {
        $this->configMock->expects($this->any())
            ->method('isManualFromToPriceFilterEnabled')
            ->willReturn($isManualFromToPriceFilterEnabled);

        $this->assertEquals($expectedResult, $this->model->getFromToCondition($from, $to));
    }

    /**
     * @return array
     */
    public function getFromToConditionDataProvider()
    {
        return [
            [
                'from' => 10,
                'to' => 25,
                'isManualFromToPriceFilterEnabled' => false,
                'expectedResult' => [
                    'from' => 10,
                    'to' => 24.999
                ]
            ],
            [
                'from' => 10,
                'to' => 25,
                'isManualFromToPriceFilterEnabled' => true,
                'expectedResult' => [
                    'from' => 10,
                    'to' => 25
                ]
            ],
            [
                'from' => '10',
                'to' => '25',
                'isManualFromToPriceFilterEnabled' => false,
                'expectedResult' => [
                    'from' => 10,
                    'to' => 24.999
                ]
            ],
            [
                'from' => '10',
                'to' => '25',
                'isManualFromToPriceFilterEnabled' => true,
                'expectedResult' => [
                    'from' => 10,
                    'to' => 25
                ]
            ],
            [
                'from' => 10.45,
                'to' => 25.88,
                'isManualFromToPriceFilterEnabled' => false,
                'expectedResult' => [
                    'from' => 10.45,
                    'to' => 25.879
                ]
            ],
            [
                'from' => 10.45,
                'to' => 25.88,
                'isManualFromToPriceFilterEnabled' => true,
                'expectedResult' => [
                    'from' => 10.45,
                    'to' => 25.88
                ]
            ],
        ];
    }
}
