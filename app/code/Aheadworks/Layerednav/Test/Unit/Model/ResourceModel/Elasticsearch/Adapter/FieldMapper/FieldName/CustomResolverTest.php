<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolver;
use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolverInterface;
use Magento\Framework\DataObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolver
 */
class CustomResolverTest extends TestCase
{
    /**
     * @var CustomResolver
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

        $this->model = $objectManager->getObject(CustomResolver::class, []);
    }

    /**
     * Test getFieldName method
     */
    public function testGetFieldName()
    {
        $resolverOneMock = $this->getResolverMock('one', [], '<one>', false);
        $resolverTwoMock = $this->getResolverMock('two', [], '<two>', true);
        $resolvers = [
            'one' => $resolverOneMock,
            'two' => $resolverTwoMock
        ];
        $this->setProperty('resolvers', $resolvers);

        $this->assertEquals('<two>', $this->model->getFieldName('two', []));
    }

    /**
     * Test getFieldName method if no resolver found
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Custom field can't be processed
     */
    public function testGetFieldNameNoResolver()
    {
        $resolverOneMock = $this->getResolverMock('one', [], '<one>', false);
        $resolverTwoMock = $this->getResolverMock('two', [], '<two>', false);
        $resolvers = [
            'one' => $resolverOneMock,
            'two' => $resolverTwoMock
        ];
        $this->setProperty('resolvers', $resolvers);

        $this->model->getFieldName('three', []);
    }

    /**
     * Test getFieldName method if bad resolver specified
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Custom field name resolver must implement
     * Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolverInterface
     */
    public function testGetFieldNameBadResolver()
    {
        $badResolverMock = $this->createMock(DataObject::class);
        $resolvers = [
            'bad' => $badResolverMock,
        ];
        $this->setProperty('resolvers', $resolvers);

        $this->model->getFieldName('bad', []);
    }

    /**
     * Test isCanBeProcessed method
     *
     * @param CustomResolverInterface[] $resolvers
     * @param string $attributeCode
     * @param bool $expectedResult
     * @dataProvider isCanBeProcessedDataProvider
     */
    public function testIsCanBeProcessed($resolvers, $attributeCode, $expectedResult)
    {
        $this->setProperty('resolvers', $resolvers);

        $this->assertEquals($expectedResult, $this->model->isCanBeProcessed($attributeCode));
    }

    /**
     * @return array
     */
    public function isCanBeProcessedDataProvider()
    {
        return [
            [
                'resolvers' => [],
                'attributeCode' => 'custom',
                'expectedResult' => false
            ],
            [
                'resolvers' => [
                    'other' => $this->createMock(CustomResolverInterface::class)
                ],
                'attributeCode' => 'custom',
                'expectedResult' => false
            ],
            [
                'resolvers' => [
                    'custom' => $this->createMock(CustomResolverInterface::class)
                ],
                'attributeCode' => 'custom',
                'expectedResult' => true
            ],
            [
                'resolvers' => [
                    'other' => $this->createMock(CustomResolverInterface::class),
                    'custom' => $this->createMock(CustomResolverInterface::class)
                ],
                'attributeCode' => 'custom',
                'expectedResult' => true
            ]
        ];
    }

    /**
     * Get resolver mock
     *
     * @param string $attributeCode
     * @param array $context
     * @param string $result
     * @param bool|false $called
     * @return CustomResolverInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getResolverMock($attributeCode, array $context, $result, $called = false)
    {
        $resolverMock = $this->createMock(CustomResolverInterface::class);

        if ($called) {
            $resolverMock->expects($this->once())
                ->method('getFieldName')
                ->with($attributeCode, $context)
                ->willReturn($result);
        } else {
            $resolverMock->expects($this->never())
                ->method('getFieldName');
        }

        return $resolverMock;
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
