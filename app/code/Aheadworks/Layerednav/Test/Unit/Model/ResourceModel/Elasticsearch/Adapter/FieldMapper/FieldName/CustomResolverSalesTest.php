<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolver\Sales;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolver\Sales
 */
class CustomResolverSalesTest extends TestCase
{
    /**
     * @var Sales
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

        $this->model = $objectManager->getObject(Sales::class, []);
    }

    /**
     * Test getFieldName method
     *
     * @param string $attributeCode
     * @param array $context
     * @param string $expectedResult
     * @dataProvider getFieldNameDataProvider
     */
    public function testGetFieldName($attributeCode, $context, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->model->getFieldName($attributeCode, $context));
    }

    /**
     * @return array
     */
    public function getFieldNameDataProvider()
    {
        return [
            [
                'attributeCode' => 'custom',
                'context' => [],
                'expectedResult' => 'custom_0_0'
            ],
            [
                'attributeCode' => 'custom',
                'context' => [
                    'website_id' => 5
                ],
                'expectedResult' => 'custom_0_5'
            ],
            [
                'attributeCode' => 'custom',
                'context' => [
                    'customer_group_id' => 125
                ],
                'expectedResult' => 'custom_125_0'
            ],
            [
                'attributeCode' => 'custom',
                'context' => [
                    'website_id' => 5,
                    'customer_group_id' => 125
                ],
                'expectedResult' => 'custom_125_5'
            ],
        ];
    }
}
