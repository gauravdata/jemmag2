<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin\Elasticsearch;

use Aheadworks\Layerednav\Plugin\Elasticsearch\DynamicAggregationBuilder;
use Aheadworks\Layerednav\Model\Config;
use Magento\Elasticsearch\SearchAdapter\Aggregation\Builder\Dynamic;
use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Plugin\Elasticsearch\DynamicAggregationBuilder
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
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);

        $this->plugin = $objectManager->getObject(
            DynamicAggregationBuilder::class,
            [
                'config' => $this->configMock,
            ]
        );
    }

    /**
     * Test afterBuild method
     *
     * @param array $result
     * @param bool $isManualFromToEnabled
     * @param string $bucketName
     * @param array $queryResult
     * @param array $expectedResult
     * @dataProvider afterBuildDataProvider
     */
    public function testAfterBuild(
        $result,
        $isManualFromToEnabled,
        $bucketName,
        $queryResult,
        $expectedResult
    ) {
        $subjectMock = $this->createMock(Dynamic::class);

        $this->configMock->expects($this->once())
            ->method('isManualFromToPriceFilterEnabled')
            ->willReturn($isManualFromToEnabled);

        $bucketMock = $this->createMock(RequestBucketInterface::class);
        $bucketMock->expects($this->any())
            ->method('getName')
            ->willReturn($bucketName);

        $dataProviderMock = $this->createMock(DataProviderInterface::class);

        $this->assertEquals(
            $expectedResult,
            $this->plugin->afterBuild(
                $subjectMock,
                $result,
                $bucketMock,
                [],
                $queryResult,
                $dataProviderMock
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
                'queryResult' => [],
                'expectedResult' => ['default-build-result']
            ],
            [
                'result' => ['default-build-result'],
                'isManualFromToPriceFilterEnabled' => true,
                'bucketName' => 'cost_bucket',
                'queryResult' => [],
                'expectedResult' => ['default-build-result']
            ],
            [
                'result' => ['default-build-result'],
                'isManualFromToPriceFilterEnabled' => true,
                'bucketName' => 'price_bucket',
                'queryResult' => [
                    'aggregations' => [
                        'price_bucket' => [
                            'min' => 25,
                            'max' => 99,
                            'count' => 5
                        ]
                    ]
                ],
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
                'queryResult' => [
                    'aggregations' => [
                        'price_bucket' => []
                    ]
                ],
                'expectedResult' => [
                    'default-build-result',
                    'stats' => [
                        'value' => 'stats',
                        'min' => 0,
                        'max' => 0,
                        'count' => 0,
                    ]
                ]
            ],
            [
                'result' => ['default-build-result'],
                'isManualFromToPriceFilterEnabled' => true,
                'bucketName' => 'price_bucket',
                'queryResult' => [
                    'aggregations' => []
                ],
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
