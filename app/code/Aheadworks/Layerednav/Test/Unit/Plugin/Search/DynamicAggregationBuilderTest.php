<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin\Search;

use Aheadworks\Layerednav\Plugin\Search\DynamicAggregationBuilder;
use Aheadworks\Layerednav\Model\Config;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Dynamic;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderInterface;
use Magento\Framework\Search\Dynamic\DataProviderInterface as DynamicDataProviderInterface;
use Magento\Framework\Search\Dynamic\EntityStorage;
use Magento\Framework\Search\Dynamic\EntityStorageFactory;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Plugin\Search\DynamicAggregationBuilder
 */
class DynamicAggregationBuilderTest extends TestCase
{
    /**
     * @var DynamicAggregationBuilder
     */
    private $plugin;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var DynamicDataProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataProviderMock;

    /**
     * @var EntityStorageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityStorageFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->dataProviderMock = $this->createMock(DynamicDataProviderInterface::class);
        $this->entityStorageFactoryMock = $this->createMock(EntityStorageFactory::class);

        $this->plugin = $objectManager->getObject(
            DynamicAggregationBuilder::class,
            [
                'config' => $this->configMock,
                'dataProvider' => $this->dataProviderMock,
                'entityStorageFactory' => $this->entityStorageFactoryMock
            ]
        );
    }

    /**
     * Test afterBuild method
     *
     * @param array $result
     * @param bool $isManualFromToEnabled
     * @param string $bucketName
     * @param array $aggregations
     * @param bool $isTriggered
     * @param array $expectedResult
     * @dataProvider afterBuildDataProvider
     */
    public function testAfterBuild(
        $result,
        $isManualFromToEnabled,
        $bucketName,
        $aggregations,
        $isTriggered,
        $expectedResult
    ) {
        $subjectMock = $this->createMock(Dynamic::class);

        $this->configMock->expects($this->once())
            ->method('isManualFromToPriceFilterEnabled')
            ->willReturn($isManualFromToEnabled);

        $dataProviderMock = $this->createMock(DataProviderInterface::class);

        $bucketMock = $this->createMock(RequestBucketInterface::class);
        $bucketMock->expects($this->any())
            ->method('getName')
            ->willReturn($bucketName);

        $entityIdsTableMock = $this->createMock(Table::class);

        if ($isTriggered) {
            $entityStorageMock = $this->createMock(EntityStorage::class);
            $this->entityStorageFactoryMock->expects($this->once())
                ->method('create')
                ->with($entityIdsTableMock)
                ->willReturn($entityStorageMock);

            $this->dataProviderMock->expects($this->once())
                ->method('getAggregations')
                ->with($entityStorageMock)
                ->willReturn($aggregations);
        } else {
            $this->entityStorageFactoryMock->expects($this->never())
                ->method('create');

            $this->dataProviderMock->expects($this->never())
                ->method('getAggregations');
        }

        $this->assertEquals(
            $expectedResult,
            $this->plugin->afterBuild(
                $subjectMock,
                $result,
                $dataProviderMock,
                [],
                $bucketMock,
                $entityIdsTableMock
            )
        );
    }

    /**
     * @return array
     */
    public function afterBuildDataProvider()
    {
        return [
            [
                'result' => ['default-build-result'],
                'isManualFromToPriceFilterEnabled' => false,
                'bucketName' => 'price_bucket',
                'aggregations' => [],
                'isTriggered' => false,
                'expectedResult' => ['default-build-result']
            ],
            [
                'result' => ['default-build-result'],
                'isManualFromToPriceFilterEnabled' => true,
                'bucketName' => 'cost_bucket',
                'aggregations' => [],
                'isTriggered' => false,
                'expectedResult' => ['default-build-result']
            ],
            [
                'result' => ['default-build-result'],
                'isManualFromToPriceFilterEnabled' => true,
                'bucketName' => 'price_bucket',
                'aggregations' => [
                    'min' => 25,
                    'max' => 99,
                    'count' => 5
                ],
                'isTriggered' => true,
                'expectedResult' => [
                    'default-build-result',
                    'stats' => [
                        'value' => 'stats',
                        'min' => 25,
                        'max' => 99,
                        'count' => 5,
                    ]
                ]
            ],
            [
                'result' => ['default-build-result'],
                'isManualFromToPriceFilterEnabled' => true,
                'bucketName' => 'price_bucket',
                'aggregations' => [],
                'isTriggered' => true,
                'expectedResult' => [
                    'default-build-result',
                    'stats' => [
                        'value' => 'stats',
                        'min' => 0,
                        'max' => 0,
                        'count' => 0,
                    ]
                ]
            ]
        ];
    }
}
