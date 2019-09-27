<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\ObjectManagerInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderFactory
 */
class DataBuilderFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DataBuilderFactory
     */
    private $model;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->getMockBuilder(ObjectManagerInterface::class)
            ->getMockForAbstractClass();

        $this->model = $objectManager->getObject(
            DataBuilderFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
            ]
        );
    }

    /**
     * Test create method
     */
    public function testCreate()
    {
        $class = 'DataBuilderClass';

        $dataBuilderMock = $this->getMockBuilder(DataBuilderInterface::class)
            ->getMockForAbstractClass();

        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($class)
            ->willReturn($dataBuilderMock);

        $this->assertEquals($dataBuilderMock, $this->model->create($class));
    }
}
