<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model;

use Aheadworks\Layerednav\Model\DateResolver;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\DateResolver
 */
class DateResolverTest extends TestCase
{
    /**
     * @var DateResolver
     */
    private $model;

    /**
     * @var TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $timezoneMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->timezoneMock = $this->createMock(TimezoneInterface::class);

        $this->model = $objectManager->getObject(
            DateResolver::class,
            [
                'timezone' => $this->timezoneMock,
            ]
        );
    }

    /**
     * Test getDateInDbFormat method
     *
     * @param $currentTime
     * @param $hour
     * @param $minute
     * @param $seconds
     * @param $expectedResult
     * @dataProvider getDateInDbFormatDataProvider
     */
    public function testGetDateInDbFormat($currentTime, $hour, $minute, $seconds, $expectedResult)
    {
        $datetimeMock = $this->createMock(\DateTime::class);
        $this->timezoneMock->expects($this->once())
            ->method('date')
            ->willReturn($datetimeMock);

        if (!$currentTime) {
            $datetimeMock->expects($this->once())
                ->method('setTime')
                ->with($hour, $minute, $seconds)
                ->willReturnSelf();
        } else {
            $datetimeMock->expects($this->never())
                ->method('setTime');
        }

        $datetimeMock->expects($this->once())
            ->method('format')
            ->with(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
            ->willReturn($expectedResult);

        $this->assertEquals($expectedResult, $this->model->getDateInDbFormat($currentTime, $hour, $minute, $seconds));
    }

    /**
     * @return array
     */
    public function getDateInDbFormatDataProvider()
    {
        return [
            [
                'currentTime' => true,
                'hour' => 1,
                'minute' => 2,
                'seconds' => 3,
                'expectedResult' => '2019-01-02 09:10:11'
            ],
            [
                'currentTime' => false,
                'hour' => 1,
                'minute' => 2,
                'seconds' => 3,
                'expectedResult' => '2019-01-02 01:02:03'
            ],
        ];
    }
}
