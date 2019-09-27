<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Layer\Filter\Applier\ApplierInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Pool;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Applier\Pool
 */
class PoolTest extends TestCase
{
    /**
     * @var Pool
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

        $this->model = $objectManager->getObject(Pool::class, []);
    }

    /**
     * Test getApplier method
     */
    public function testGetApplier()
    {
        $applierMock = $this->createMock(ApplierInterface::class);
        $appliers = [
            'test' => $applierMock
        ];
        $this->setProperty('appliers', $appliers);

        $this->assertSame($applierMock, $this->model->getApplier('test'));
    }

    /**
     * Test getApplier method if no applier found
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Unknown filter type: test-not-found requested
     */
    public function testGetApplierNoApplier()
    {
        $applierMock = $this->createMock(ApplierInterface::class);
        $appliers = [
            'test' => $applierMock
        ];
        $this->setProperty('appliers', $appliers);

        $this->model->getApplier('test-not-found');
    }

    /**
     * Test getApplier method if bad applier specified
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Applier must implement
     * Aheadworks\Layerednav\Model\Layer\Filter\Applier\ApplierInterface interface
     */
    public function testGetApplierBadApplier()
    {
        $applierMock = $this->createMock(DataObject::class);
        $appliers = [
            'bad' => $applierMock
        ];
        $this->setProperty('appliers', $appliers);

        $this->model->getApplier('bad');
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
