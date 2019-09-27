<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Factory;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Magento\Framework\DataObject;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Factory
 */
class FactoryTest extends TestCase
{
    /**
     * @var Factory
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

        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);

        $this->model = $objectManager->getObject(
            Factory::class,
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
        $type = 'SomeDataProviderClass';

        $dataBuilderMock = $this->createMock(DataBuilderInterface::class);

        $dataProviderMock = $this->createMock(DataProviderInterface::class);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($type, ['itemDataBuilder' => $dataBuilderMock])
            ->willReturn($dataProviderMock);

        $this->assertSame($dataProviderMock, $this->model->create($type, $dataBuilderMock));
    }

    /**
     * Test create method if bad data provider class specified
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Type must implement Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface
     * interface
     */
    public function testCreateBadDataProvider()
    {
        $badType = 'BadProviderClass';

        $dataBuilderMock = $this->createMock(DataBuilderInterface::class);

        $badDataProviderMock = $this->createMock(DataObject::class);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with($badType, ['itemDataBuilder' => $dataBuilderMock])
            ->willReturn($badDataProviderMock);

        $this->model->create($badType, $dataBuilderMock);
    }
}
