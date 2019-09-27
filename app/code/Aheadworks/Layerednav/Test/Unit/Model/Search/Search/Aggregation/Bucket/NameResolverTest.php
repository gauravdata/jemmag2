<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Search\Aggregation\Bucket;

use Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket\NameResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Search\Aggregation\Bucket\NameResolver
 */
class NameResolverTest extends TestCase
{
    /**
     * @var NameResolver
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

        $this->model = $objectManager->getObject(NameResolver::class, []);
    }

    /**
     * Test getName method
     *
     * @param string $field
     * @param string $expectedResult
     * @dataProvider getNameDataProvider
     */
    public function testIsExtendedSearchNeeded($field, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->model->getName($field));
    }

    /**
     * @return array
     */
    public function getNameDataProvider()
    {
        return [
            [
                'field' => 'test',
                'expectedResult' => 'test_bucket'
            ],
            [
                'field' => 'field',
                'expectedResult' => 'field_bucket'
            ],
            [
                'field' => 'category_ids_query',
                'expectedResult' => 'category_bucket'
            ],
        ];
    }
}
