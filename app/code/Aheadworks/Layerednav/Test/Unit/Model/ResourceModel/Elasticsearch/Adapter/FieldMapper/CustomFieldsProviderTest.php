<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider;
use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldsProviderInterface;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider
 */
class CustomFieldsProviderTest extends TestCase
{
    /**
     * @var CustomFieldsProvider
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

        $this->model = $objectManager->getObject(CustomFieldsProvider::class, []);
    }

    /**
     * Test getFields method
     */
    public function testGetFields()
    {
        $context = [];
        $expectedResult = [
            'field_one' => ['data-one'],
            'field_two' => ['data-two']
        ];

        $providerOneMock = $this->getFieldsProviderMock($context, ['field_one' => ['data-one']]);
        $providerTwoMock = $this->getFieldsProviderMock($context, ['field_two' => ['data-two']]);

        $providers = [$providerOneMock, $providerTwoMock];
        $this->setProperty('providers', $providers);

        $this->assertEquals($expectedResult, $this->model->getFields($context));
    }

    /**
     * Test getFields method if no providers specified
     */
    public function testGetFieldsNoProviders()
    {
        $context = [];
        $expectedResult = [];

        $providers = [];
        $this->setProperty('providers', $providers);

        $this->assertEquals($expectedResult, $this->model->getFields($context));
    }

    /**
     * Test getFields method if bad provider specified
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Fields provider must implement
     * Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldsProviderInterface
     */
    public function testGetFieldsBadProvider()
    {
        $context = [];

        $badProviderMock = $this->createMock(DataObject::class);

        $providers = [$badProviderMock];
        $this->setProperty('providers', $providers);

        $this->model->getFields($context);
    }

    /**
     * Get fields provider mock
     *
     * @param array $context
     * @param array $fieldsData
     * @return FieldsProviderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getFieldsProviderMock(array $context, array $fieldsData)
    {
        $providerMock = $this->createMock(FieldsProviderInterface::class);
        $providerMock->expects($this->once())
            ->method('getFields')
            ->with($context)
            ->willReturn($fieldsData);

        return $providerMock;
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
