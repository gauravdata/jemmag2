<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Layer\Filter\Interval;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Interval
 */
class IntervalTest extends TestCase
{
    /**
     * @var Interval
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

        $this->model = $objectManager->getObject(Interval::class, []);
    }

    /**
     * Test __toString method
     *
     * @param $from
     * @param $to
     * @param $expectedResult
     * @dataProvider toStringDataProvider
     */
    public function testToString($from, $to, $expectedResult)
    {
        if ($from !== null) {
            $this->model->setFrom($from);
        }
        if ($to !== null) {
            $this->model->setTo($to);
        }

        $this->assertEquals($expectedResult, (string)$this->model);
    }

    /**
     * @return array
     */
    public function toStringDataProvider()
    {
        return [
            [
                'from' => 10,
                'to' => 20,
                'expectedResult' => '10-20'
            ],
            [
                'from' => 0,
                'to' => 20,
                'expectedResult' => '0-20'
            ],
            [
                'from' => null,
                'to' => 20,
                'expectedResult' => '-20'
            ],
            [
                'from' => 10,
                'to' => null,
                'expectedResult' => '10-'
            ],
            [
                'from' => null,
                'to' => null,
                'expectedResult' => '-'
            ],
        ];
    }
}
