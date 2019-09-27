<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Factory;

use Aheadworks\Layerednav\Model\Layer\Filter\Factory\DataProviderPool;
use Aheadworks\Layerednav\Model\Layer\Filter\Factory\DataProviderInterface;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Factory\DataProviderPool
 */
class DataProviderPoolTest extends TestCase
{
    /**
     * @var DataProviderPool
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

        $this->model = $objectManager->getObject(DataProviderPool::class, []);
    }

    /**
     * Test getDataProvider method
     */
    public function testGetDataProvider()
    {
        $dataProviderMock = $this->createMock(DataProviderInterface::class);
        $providers = [
            'test' => $dataProviderMock
        ];

        $this->setProperty('providers', $providers);

        $this->assertSame($dataProviderMock, $this->model->getDataProvider('test'));
    }

    /**
     * Test getDataProvider method if inknown data provider requested
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown filter type: test-unknown requested
     */
    public function testGetDataProviderUnknownRequested()
    {
        $this->setProperty('providers', []);

        $this->model->getDataProvider('test-unknown');
    }

    /**
     * Test getDataProvider method if bad data provider specified
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Factory data provider must implement
     * Aheadworks\Layerednav\Model\Layer\Filter\Factory\DataProviderInterface interface
     */
    public function testGetDataProviderBadDataProvider()
    {
        $dataProviderMock = $this->createMock(DataObject::class);
        $providers = [
            'bad' => $dataProviderMock
        ];

        $this->setProperty('providers', $providers);

        $this->model->getDataProvider('bad');
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
