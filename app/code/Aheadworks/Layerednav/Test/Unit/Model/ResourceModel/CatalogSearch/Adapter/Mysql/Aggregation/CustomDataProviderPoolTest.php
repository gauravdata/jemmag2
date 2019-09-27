<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\AggregationProviderInterface;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\CustomDataProviderPool;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Adapter\Mysql\Aggregation\CustomDataProviderPool
 */
class CustomDataProviderPoolTest extends TestCase
{
    /**
     * @var CustomDataProviderPool
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

        $this->model = $objectManager->getObject(CustomDataProviderPool::class, []);
    }

    /**
     * Test getAggregationProvider method
     */
    public function testGetAggregationProvider()
    {
        $field = 'field';
        $aggregationProviderMock = $this->createMock(AggregationProviderInterface::class);
        $providers = [
            $field => $aggregationProviderMock
        ];
        $this->setProperty('providers', $providers);

        $this->assertSame($aggregationProviderMock, $this->model->getAggregationProvider($field));
    }

    /**
     * Test getAggregationProvider method if no provider found
     */
    public function testGetAggregationProviderNoProvider()
    {
        $this->assertNull($this->model->getAggregationProvider('unknown-field'));
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
