<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search\Aggregation\Bucket;

use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket\Merger;
use Magento\Framework\Api\Search\AggregationValueInterface;
use Magento\Framework\Api\Search\BucketInterface;
use Magento\Framework\Search\Response\Aggregation\ValueFactory as AggregationValueFactory;
use Magento\Framework\Search\Response\BucketFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket\Merger
 */
class MergerTest extends TestCase
{
    /**
     * @var Merger
     */
    private $model;

    /**
     * @var AggregationValueFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $aggregationValueFactoryMock;

    /**
     * @var BucketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bucketFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->aggregationValueFactoryMock = $this->createMock(AggregationValueFactory::class);
        $this->bucketFactoryMock = $this->createMock(BucketFactory::class);

        $this->model = $objectManager->getObject(
            Merger::class,
            [
                'aggregationValueFactory' => $this->aggregationValueFactoryMock,
                'bucketFactory' => $this->bucketFactoryMock
            ]
        );
    }

    /**
     * Test merge method
     *
     * @param array $baseValues
     * @param array $extendedValues
     * @param array $expectedValues
     * @dataProvider mergeDataProvider
     * @throws \ReflectionException
     */
    public function testMerge($baseValues, $extendedValues, $expectedValues)
    {
        $baseBucketMock = $this->getBucketMock('test_bucket', $baseValues);
        $extendedBucketMock = $this->getBucketMock('test_bucket', $extendedValues);

        $expectedBucketValues = $this->getBucketValues($expectedValues);
        $expectedBucketMock = $this->getBucketMock('test_bucket', $expectedValues);

        $aggregationValuesMap = $this->getAggregationValuesMap($baseValues);
        $this->aggregationValueFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValueMap($aggregationValuesMap));

        $this->bucketFactoryMock->expects($this->once())
            ->method('create')
            ->with(['name' => 'test_bucket', 'values' => $expectedBucketValues])
            ->willReturn($expectedBucketMock);

        $result = $this->model->merge($baseBucketMock, $extendedBucketMock);
        $this->assertSame($expectedBucketMock, $result);
    }

    /**
     * @return array
     */
    public function mergeDataProvider()
    {
        return [
            [
                'baseValues' => [
                    ['value' => '11', 'count' => 21],
                    ['value' => '12', 'count' => 32],
                    ['value' => '13', 'count' => 43],
                    ['value' => '14', 'count' => 54],
                ],
                'extendedValues' => [
                    ['value' => '12', 'count' => 55],
                    ['value' => '13', 'count' => 66],
                ],
                'expectedValues' => [
                    ['value' => '11', 'count' => 0],
                    ['value' => '12', 'count' => 55],
                    ['value' => '13', 'count' => 66],
                    ['value' => '14', 'count' => 0],
                ]
            ],
            [
                'baseValues' => [
                    ['value' => '11', 'count' => 21],
                    ['value' => '12', 'count' => 32],
                    ['value' => '13', 'count' => 43],
                ],
                'extendedValues' => [
                    ['value' => '12', 'count' => 55],
                    ['value' => '21', 'count' => 66],
                ],
                'expectedValues' => [
                    ['value' => '11', 'count' => 0],
                    ['value' => '12', 'count' => 55],
                    ['value' => '13', 'count' => 0],
                ]
            ],
        ];
    }

    /**
     * Get bucket mock
     *
     * @param string $name
     * @param array $values
     * @return BucketInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getBucketMock($name, array $values)
    {
        $bucketMock = $this->createMock(BucketInterface::class);
        $bucketMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        $bucketValues = $this->getBucketValues($values);
        $bucketMock->expects($this->any())
            ->method('getValues')
            ->willReturn($bucketValues);

        return $bucketMock;
    }

    /**
     * Get bucket values
     *
     * @param array $resultValues
     * @return array
     * @throws \ReflectionException
     */
    private function getBucketValues(array $resultValues)
    {
        $resultBucketValues = [];
        foreach ($resultValues as $resultValue) {
            $resultBucketValues[] = $this->getAggregationValueMock($resultValue['value'], $resultValue['count']);
        }

        return $resultBucketValues;
    }

    /**
     * Get aggregation value mock
     *
     * @param string|array $value
     * @param string $count
     * @return AggregationValueInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getAggregationValueMock($value, $count)
    {
        $aggregationValueMock = $this->createMock(AggregationValueInterface::class);
        $aggregationValueMock->expects($this->any())
            ->method('getValue')
            ->willReturn($value);
        $aggregationValueMock->expects($this->any())
            ->method('getMetrics')
            ->willReturn([
                'value' => $value,
                'count' => $count
            ]);

        return $aggregationValueMock;
    }

    /**
     * Get aggregation values map
     *
     * @param array $values
     * @return array
     * @throws \ReflectionException
     */
    private function getAggregationValuesMap(array $values)
    {
        $aggregationValuesMap = [];
        foreach ($values as $value) {
            $aggregationValuesMap[] = [
                [
                    'value' => $value['value'],
                    'metrics' => [
                        'value' => $value['value'],
                        'count' => 0
                    ],
                ],
                $this->getAggregationValueMock($value['value'], 0)
            ];
        }
        return $aggregationValuesMap;
    }
}
