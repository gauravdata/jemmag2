<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider\BaseProvider;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for BaseProvider
 */
class BaseProviderTest extends TestCase
{
    /**
     * @var BaseProvider
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

        $this->model = $objectManager->getObject(BaseProvider::class, []);
    }

    /**
     * Test getFields method
     */
    public function testGetFields()
    {
        $context = [];
        $expectedResult = [
            'field' => [
                'type' => 'field_type'
            ],
        ];

        $this->setProperty('name', 'field');
        $this->setProperty('type', 'field_type');

        $this->assertEquals($expectedResult, $this->model->getFields($context));
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
