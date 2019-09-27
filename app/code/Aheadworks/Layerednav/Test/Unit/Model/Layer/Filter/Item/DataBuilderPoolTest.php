<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderPool;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderPool
 */
class DataBuilderPoolTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DataBuilderPool
     */
    private $model;

    /**
     * @var DataBuilderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataBuilderFactoryMock;

    /**
     * @var DataBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dataBuilderFactoryMock = $this->getMockBuilder(DataBuilderFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            DataBuilderPool::class,
            [
                'dataBuilderFactory' => $this->dataBuilderFactoryMock,
                'dataBuilders' => [
                    'default' => 'DataBuilderClass'
                ],
            ]
        );
    }

    /**
     * Test getDataBuilder method
     *
     * @throws \Exception
     */
    public function testGetDataBuilder()
    {
        $type = 'default';
        $class = 'DataBuilderClass';

        $dataBuilderMock = $this->getMockBuilder(DataBuilderInterface::class)
            ->getMockForAbstractClass();

        $this->dataBuilderFactoryMock->expects($this->once())
            ->method('create')
            ->with($class)
            ->willReturn($dataBuilderMock);

        $this->assertEquals($dataBuilderMock, $this->model->getDataBuilder($type));
    }

    /**
     * Test getDataBuilder method if specified type is not supported
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown data builder type: not_supported requested
     */
    public function testGetDataBuilderTypeNotSupported()
    {
        $type = 'not_supported';

        $this->model->getDataBuilder($type);
    }

    /**
     * Test getDataBuilder method if data builder does not implement needed interface
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Data builder instance default does not implement required interface.
     */
    public function testGetDataBuilderNotImplementInterface()
    {
        $type = 'default';
        $class = 'DataBuilderClass';

        $this->dataBuilderFactoryMock->expects($this->once())
            ->method('create')
            ->with($class)
            ->willReturn(null);

        $this->model->getDataBuilder($type);
    }
}
