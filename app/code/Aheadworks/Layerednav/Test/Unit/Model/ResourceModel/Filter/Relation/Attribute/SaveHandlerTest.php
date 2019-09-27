<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Filter\Relation\Attribute;

use Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Attribute\SaveHandler;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterManagementInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\Filter\Relation\Attribute\SaveHandler
 */
class SaveHandlerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var SaveHandler
     */
    private $model;

    /**
     * @var FilterManagementInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterManagementMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterManagementMock = $this->getMockBuilder(FilterManagementInterface::class)
            ->getMockForAbstractClass();

        $this->model = $objectManager->getObject(
            SaveHandler::class,
            [
                'filterManagement' => $this->filterManagementMock
            ]
        );
    }

    /**
     * Test execute method for attribute filters
     *
     * @param int $filterId
     * @param string $filterType
     * @param array $arguments
     * @dataProvider getAttributeFilterDataProvider
     */
    public function testExecuteAttributeFilter($filterId, $filterType, $arguments)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getId')
            ->willReturn($filterId);
        $filterMock->expects($this->once())
            ->method('getType')
            ->willReturn($filterType);

        $this->filterManagementMock->expects($this->once())
            ->method('synchronizeAttribute')
            ->with($filterId, false)
            ->willReturn(true);

        $this->assertEquals($filterMock, $this->model->execute($filterMock, $arguments));
    }

    /**
     * Test execute method for attribute filters if an error occurs
     *
     * @param int $filterId
     * @param string $filterType
     * @param array $arguments
     * @expectedException \Exception
     * @expectedExceptionMessage Can not synchronize linked attribute!
     * @dataProvider getAttributeFilterDataProvider
     */
    public function testExecuteAttributeFilterError($filterId, $filterType, $arguments)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getId')
            ->willReturn($filterId);
        $filterMock->expects($this->once())
            ->method('getType')
            ->willReturn($filterType);

        $this->filterManagementMock->expects($this->once())
            ->method('synchronizeAttribute')
            ->with($filterId, false)
            ->willReturn(false);

        $this->assertEquals($filterMock, $this->model->execute($filterMock, $arguments));
    }

    /**
     * @return array
     */
    public function getAttributeFilterDataProvider()
    {
        $filterData = [];
        $filterId = 1;
        foreach (FilterInterface::ATTRIBUTE_FILTER_TYPES as $filterType) {
            $filterData[] = [
                'filterId' => $filterId,
                'filterType' => $filterType,
                'arguments' => [
                    'is_synchronization_needed' => true,
                ],
            ];
        }
        return $filterData;
    }

    /**
     * Test execute method for custom filters
     *
     * @param string $filterType
     * @dataProvider getCustomFilterDataProvider
     */
    public function testExecuteCustomFilter($filterType)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getType')
            ->willReturn($filterType);

        $this->assertEquals($filterMock, $this->model->execute($filterMock, []));
    }

    /**
     * @return array
     */
    public function getCustomFilterDataProvider()
    {
        $filterData = [];
        foreach (FilterInterface::CUSTOM_FILTER_TYPES as $filterType) {
            $filterData[] = [
                'filterType' => $filterType
            ];
        }
        return $filterData;
    }
}
