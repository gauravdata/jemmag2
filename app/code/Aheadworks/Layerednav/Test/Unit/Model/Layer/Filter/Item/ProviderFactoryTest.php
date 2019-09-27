<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Pool as ItemDataProviderPool;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderInterface;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderFactory
 */
class ProviderFactoryTest extends TestCase
{
    /**
     * @var ProviderFactory
     */
    private $model;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * @var ItemDataProviderPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemDataProviderPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->itemDataProviderPoolMock = $this->createMock(ItemDataProviderPool::class);

        $this->model = $objectManager->getObject(
            ProviderFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
                'itemDataProviderPool' => $this->itemDataProviderPoolMock
            ]
        );
    }

    /**
     * Test create method
     */
    public function testCreate()
    {
        $type = 'type';
        $sortOrder = 'sort-order';

        $dataProviderMock = $this->createMock(DataProviderInterface::class);
        $this->itemDataProviderPoolMock->expects($this->once())
            ->method('getDataProvider')
            ->with($type, $sortOrder)
            ->willReturn($dataProviderMock);

        $providerMock = $this->createMock(ProviderInterface::class);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(ProviderInterface::class, ['dataProvider' => $dataProviderMock])
            ->willReturn($providerMock);

        $this->assertSame($providerMock, $this->model->create($type, $sortOrder));
    }

    /**
     * Test create method if an exception occurs
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testCreateException()
    {
        $type = 'type';
        $sortOrder = 'sort-order';

        $this->itemDataProviderPoolMock->expects($this->once())
            ->method('getDataProvider')
            ->with($type, $sortOrder)
            ->willThrowException(new \Exception('Error!'));

        $this->objectManagerMock->expects($this->never())
            ->method('create');

        $this->model->create($type, $sortOrder);
    }
}
