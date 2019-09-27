<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search\Aggregation;

use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket\Merger as BucketMerger;
use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Merger;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\AggregationInterfaceFactory;
use Magento\Framework\Api\Search\BucketInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\Aggregation\Merger
 */
class MergerTest extends TestCase
{
    /**
     * @var Merger
     */
    private $model;

    /**
     * @var AggregationInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $aggregationFactoryMock;

    /**
     * @var BucketMerger|\PHPUnit_Framework_MockObject_MockObject
     */
    private $bucketMergerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->aggregationFactoryMock = $this->createMock(AggregationInterfaceFactory::class);
        $this->bucketMergerMock = $this->createMock(BucketMerger::class);

        $this->model = $objectManager->getObject(
            Merger::class,
            [
                'aggregationFactory' => $this->aggregationFactoryMock,
                'bucketMerger' => $this->bucketMergerMock
            ]
        );
    }

    /**
     * Test merge method
     */
    public function testMerge()
    {
        $baseBucketNames = ['bucket1', 'bucket2', 'bucket3'];
        $baseAggregationMock = $this->getAggregationMock($baseBucketNames);

        $aggregationOneBucketNames = ['bucket1', 'bucket5', 'bucket3'];
        $aggregationTwoBucketNames = ['bucket10'];
        $aggregationThreeBucketNames = [];
        $aggregations = [
            $this->getAggregationMock($aggregationOneBucketNames),
            $this->getAggregationMock($aggregationTwoBucketNames),
            $this->getAggregationMock($aggregationThreeBucketNames)
        ];

        $expectedBucketNames = ['bucket1', 'bucket2', 'bucket3', 'bucket5', 'bucket10'];
        $expectedBuckets = [];
        foreach ($expectedBucketNames as $expectedBucketName) {
            $expectedBuckets[$expectedBucketName] = $this->getBucketMock($expectedBucketName);
        }

        $this->bucketMergerMock->expects($this->never())
            ->method('merge');

        $resultAggregationMock = $this->getAggregationMock([]);
        $this->aggregationFactoryMock->expects($this->once())
            ->method('create')
            ->with(['buckets' => $expectedBuckets])
            ->willReturn($resultAggregationMock);

        $this->assertSame(
            $resultAggregationMock,
            $this->model->merge($baseAggregationMock, $aggregations)
        );
    }

    /**
     * Test merge method if merge buckets enabled
     */
    public function testMergeBucketsMerge()
    {
        $baseBucketNames = ['bucket1', 'bucket2', 'bucket3'];
        $baseAggregationMock = $this->getAggregationMock($baseBucketNames);

        $aggregationOneBucketNames = ['bucket1', 'bucket5', 'bucket3'];
        $aggregationTwoBucketNames = ['bucket10'];
        $aggregationThreeBucketNames = [];
        $aggregations = [
            $this->getAggregationMock($aggregationOneBucketNames),
            $this->getAggregationMock($aggregationTwoBucketNames),
            $this->getAggregationMock($aggregationThreeBucketNames)
        ];

        $expectedBucketNames = ['bucket1', 'bucket2', 'bucket3', 'bucket5', 'bucket10'];
        $expectedBuckets = [];
        foreach ($expectedBucketNames as $expectedBucketName) {
            $expectedBuckets[$expectedBucketName] = $this->getBucketMock($expectedBucketName);
        }

        $mergedBucketMock = $this->createMock(BucketInterface::class);
        $this->bucketMergerMock->expects($this->exactly(2))
            ->method('merge')
            ->willReturn($mergedBucketMock);

        $resultAggregationMock = $this->getAggregationMock([]);
        $this->aggregationFactoryMock->expects($this->once())
            ->method('create')
            ->with(['buckets' => $expectedBuckets])
            ->willReturn($resultAggregationMock);

        $this->assertSame(
            $resultAggregationMock,
            $this->model->merge($baseAggregationMock, $aggregations, true)
        );
    }

    /**
     * Get aggregation mock
     *
     * @param array $bucketNames
     * @return AggregationInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getAggregationMock(array $bucketNames)
    {
        $buckets = [];
        foreach ($bucketNames as $bucketName) {
            $buckets[$bucketName] = $this->getBucketMock($bucketName);
        }

        $aggregationMock = $this->createMock(AggregationInterface::class);
        $aggregationMock->expects($this->any())
            ->method('getBuckets')
            ->willReturn($buckets);

        return $aggregationMock;
    }

    /**
     * Get bucket mock
     *
     * @param string $name
     * @return BucketInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getBucketMock($name)
    {
        $bucketMock = $this->createMock(BucketInterface::class);
        $bucketMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        return $bucketMock;
    }
}
