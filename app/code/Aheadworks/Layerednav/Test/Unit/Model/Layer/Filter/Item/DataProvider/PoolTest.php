<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Pool;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderPool;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Factory as DataProviderFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Pool
 */
class PoolTest extends TestCase
{
    /**
     * @var Pool
     */
    private $model;

    /**
     * @var DataBuilderPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataBuilderPoolMock;

    /**
     * @var DataProviderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProviderFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dataBuilderPoolMock = $this->createMock(DataBuilderPool::class);
        $this->dataProviderFactoryMock = $this->createMock(DataProviderFactory::class);

        $this->model = $objectManager->getObject(
            Pool::class,
            [
                'dataBuilderPool' => $this->dataBuilderPoolMock,
                'dataProviderFactory' => $this->dataProviderFactoryMock
            ]
        );
    }

    /**
     * Test getDataProvider method
     */
    public function testGetDataProvider()
    {
        $type = 'test';
        $sortOrder = 'test-sort-order';

        $providers = [
            $type => DataProviderInterface::class
        ];
        $this->setProperty('providers', $providers);

        $dataBuilderMock = $this->createMock(DataBuilderInterface::class);
        $this->dataBuilderPoolMock->expects($this->once())
            ->method('getDataBuilder')
            ->with($sortOrder)
            ->willReturn($dataBuilderMock);

        $dataProviderMock = $this->createMock(DataProviderInterface::class);
        $this->dataProviderFactoryMock->expects($this->once())
            ->method('create')
            ->with(DataProviderInterface::class, $dataBuilderMock)
            ->willReturn($dataProviderMock);

        $this->assertSame($dataProviderMock, $this->model->getDataProvider($type, $sortOrder));
    }

    /**
     * Test getDataProvider method if no provider for specified type
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown data provider type: test requested
     */
    public function testGetDataProviderNoProvider()
    {
        $type = 'test';
        $sortOrder = 'test-sort-order';

        $providers = [];
        $this->setProperty('providers', $providers);

        $this->dataBuilderPoolMock->expects($this->never())
            ->method('getDataBuilder');

        $this->dataProviderFactoryMock->expects($this->never())
            ->method('create');

        $this->model->getDataProvider($type, $sortOrder);
    }

    /**
     * Test getDataProvider method if an error with data builder occurs
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testGetDataProviderBadDataBuilder()
    {
        $type = 'test';
        $sortOrder = 'test-sort-order';

        $providers = [
            $type => DataProviderInterface::class
        ];
        $this->setProperty('providers', $providers);

        $this->dataBuilderPoolMock->expects($this->once())
            ->method('getDataBuilder')
            ->with($sortOrder)
            ->willThrowException(new \Exception('Error!'));

        $this->dataProviderFactoryMock->expects($this->never())
            ->method('create');

        $this->model->getDataProvider($type, $sortOrder);
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->model, $value);

        return $this;
    }
}
