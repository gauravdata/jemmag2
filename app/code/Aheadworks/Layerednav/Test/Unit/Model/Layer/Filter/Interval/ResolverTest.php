<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Interval;

use Aheadworks\Layerednav\Model\Layer\Filter\Interval;
use Aheadworks\Layerednav\Model\Layer\Filter\Interval\Resolver;
use Aheadworks\Layerednav\Model\Layer\Filter\IntervalFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Interval\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * @var IntervalFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $intervalFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->intervalFactoryMock = $this->createMock(IntervalFactory::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'intervalFactory' => $this->intervalFactoryMock,
            ]
        );
    }

    /**
     * Test getInterval method
     *
     * @param string $filterData
     * @param string $from
     * @param string $to
     * @dataProvider getIntervalDataProvider
     * @throws \ReflectionException
     */
    public function testGetInterval($filterData, $from, $to)
    {
        $intervalMock = $this->createMock(Interval::class);
        $intervalMock->expects($this->once())
            ->method('setFrom')
            ->with($from)
            ->willReturnSelf();
        $intervalMock->expects($this->once())
            ->method('setTo')
            ->with($to)
            ->willReturnSelf();

        $this->intervalFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($intervalMock);

        $this->assertSame($intervalMock, $this->model->getInterval($filterData));
    }

    /**
     * @return array
     */
    public function getIntervalDataProvider()
    {
        return [
            [
                'filterData' => '1.00-2.00',
                'from' => '1.00',
                'to' => '2.00'
            ],
            [
                'filterData' => '0-100',
                'from' => '0',
                'to' => '100'
            ],
            [
                'filterData' => '500-',
                'from' => '500',
                'to' => ''
            ],
            [
                'filterData' => '-100',
                'from' => '',
                'to' => '100'
            ],
        ];
    }

    /**
     * Test getInterval method if an interval is not valid
     *
     * @param string $filterData
     * @dataProvider getIntervalNotValidDataProvider
     */
    public function testGetIntervalNotValid($filterData)
    {
        $this->intervalFactoryMock->expects($this->never())
            ->method('create');

        $this->assertFalse($this->model->getInterval($filterData));
    }

    /**
     * @return array
     */
    public function getIntervalNotValidDataProvider()
    {
        return [
            ['filterData' => ''],
            ['filterData' => '100'],
            ['filterData' => '100-AAA'],
            ['filterData' => 'BBB'],
            ['filterData' => 'AAA-BBB'],
        ];
    }
}
