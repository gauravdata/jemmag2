<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Request\Container;

use Aheadworks\Layerednav\Model\Search\Request\Container\Duplicator;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Request\Container\Duplicator
 */
class DuplicatorTest extends TestCase
{
    /**
     * @var Duplicator
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

        $this->model = $objectManager->getObject(Duplicator::class, []);
    }

    /**
     * Test perform method
     *
     * @param array $data
     * @param string $srcName
     * @param string $destName
     * @param bool $skipDynamicAggregations
     * @param array $expectedResult
     * @dataProvider performDataProvider
     */
    public function testPerform($data, $srcName, $destName, $skipDynamicAggregations, $expectedResult)
    {
        $this->assertEquals(
            $expectedResult,
            $this->model->perform($data, $srcName, $destName, $skipDynamicAggregations)
        );
    }

    /**
     * @return array
     */
    public function performDataProvider()
    {
        return [
            [
                'data' => [
                    'queries' => [
                        'catalog_view_container' => ['name' => 'catalog_view_container']
                    ],
                    'aggregations' => [
                        'price_bucket' => ['type' => 'dynamicBucket'],
                        'category_bucket' => ['type' => 'termBucket']
                    ],
                    'query' => 'catalog_view_container',
                ],
                'srcName' => 'catalog_view_container',
                'destName' => 'catalog_view_container_base',
                'skipDynamicAggregations' => true,
                'expectedResult' => [
                    'queries' => [
                        'catalog_view_container_base' => ['name' => 'catalog_view_container_base']
                    ],
                    'aggregations' => [
                        'category_bucket' => ['type' => 'termBucket']
                    ],
                    'query' => 'catalog_view_container_base'
                ]
            ],
            [
                'data' => [
                    'queries' => [
                        'catalog_view_container' => ['name' => 'catalog_view_container']
                    ],
                    'aggregations' => [
                        'price_bucket' => ['type' => 'dynamicBucket'],
                        'category_bucket' => ['type' => 'termBucket']
                    ],
                    'query' => 'catalog_view_container',
                ],
                'srcName' => 'catalog_view_container',
                'destName' => 'catalog_view_container_base',
                'skipDynamicAggregations' => false,
                'expectedResult' => [
                    'queries' => [
                        'catalog_view_container_base' => ['name' => 'catalog_view_container_base']
                    ],
                    'aggregations' => [
                        'price_bucket' => ['type' => 'dynamicBucket'],
                        'category_bucket' => ['type' => 'termBucket']
                    ],
                    'query' => 'catalog_view_container_base'
                ]
            ],
        ];
    }

    /**
     * Test perform method if an error occurs
     *
     * @param array $data
     * @dataProvider performErrorDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Bad container data specified!
     */
    public function testPerformError($data)
    {
        $this->model->perform($data, 'src', 'dest', true);
    }

    /**
     * @return array
     */
    public function performErrorDataProvider()
    {
        return [
            [
                'data' => []
            ],
            [
                'data' => [
                    'query' => 'catalog_view_container',
                ]
            ],
            [
                'data' => [
                    'query' => 'catalog_view_container',
                    'queries' => [
                        'catalog_view_container' => ['name' => 'catalog_view_container']
                    ],
                ]
            ]
        ];
    }
}
