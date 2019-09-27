<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Test\Unit\Model\Source;

use Aheadworks\Followupemail2\Model\Source\OrderStatuses;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order\Config as OrderConfig;

/**
 * Test for \Aheadworks\Followupemail2\Model\Source\OrderStatuses
 */
class OrderStatusesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OrderStatuses
     */
    private $model;

    /**
     * @var OrderConfig|\PHPUnit_Framework_MockObject_MockObject
     */
    private $orderConfigMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->orderConfigMock = $this->getMockBuilder(OrderConfig::class)
            ->setMethods(['getStateStatuses', 'getStatuses'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            OrderStatuses::class,
            [
                'orderConfig' => $this->orderConfigMock,
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $statuses = [
            'pending' => __('Pending'),
            'processing' => __('Processing'),
            'complete' => __('Complete')
        ];
        $result = [
            ['value' => 'pending', 'label' => __('Pending')],
            ['value' => 'processing', 'label' => __('Processing')],
            ['value' => 'complete', 'label' => __('Complete')],
        ];

        $this->orderConfigMock->expects($this->once())
            ->method('getStateStatuses')
            ->willReturn($statuses);

        $this->assertEquals($result, $this->model->toOptionArray());
    }
}
