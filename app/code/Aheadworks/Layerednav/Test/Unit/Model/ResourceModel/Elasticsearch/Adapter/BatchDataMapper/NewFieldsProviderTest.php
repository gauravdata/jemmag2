<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper;

use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper\NewFieldsProvider;
use Aheadworks\Layerednav\Model\ResourceModel\Product\Collection\NewProvider;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper\NewFieldsProvider
 */
class NewFieldsProviderTest extends TestCase
{
    /**
     * @var NewFieldsProvider
     */
    private $model;

    /**
     * @var NewProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $providerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->providerMock = $this->createMock(NewProvider::class);

        $this->model = $objectManager->getObject(
            NewFieldsProvider::class,
            [
                'provider' => $this->providerMock,
            ]
        );
    }

    /**
     * Test getFields method
     *
     * @param int[] $productIds
     * @param int[] $newProducts
     * @param array $expectedResult
     * @dataProvider getFieldsDataProvider
     */
    public function testGetFields($productIds, $newProducts, $expectedResult)
    {
        $storeId = 3;

        $this->providerMock->expects($this->any())
            ->method('getProductIds')
            ->with(false, $storeId)
            ->willReturn($newProducts);

        $this->assertEquals($expectedResult, $this->model->getFields($productIds, $storeId));
    }

    /**
     * @return array
     */
    public function getFieldsDataProvider()
    {
        return [
            [
                'productIds' => [125, 126, 127],
                'newProducts' => [21, 23, 126, 5050],
                'expectedResult' => [
                    125 => ['aw_new' => FilterInterface::CUSTOM_FILTER_VALUE_NO],
                    126 => ['aw_new' => FilterInterface::CUSTOM_FILTER_VALUE_YES],
                    127 => ['aw_new' => FilterInterface::CUSTOM_FILTER_VALUE_NO]
                ]
            ],
            [
                'productIds' => [125, 126, 127],
                'newProducts' => [],
                'expectedResult' => [
                    125 => ['aw_new' => FilterInterface::CUSTOM_FILTER_VALUE_NO],
                    126 => ['aw_new' => FilterInterface::CUSTOM_FILTER_VALUE_NO],
                    127 => ['aw_new' => FilterInterface::CUSTOM_FILTER_VALUE_NO]
                ]
            ],
            [
                'productIds' => [],
                'newProducts' => [21, 23, 126, 5050],
                'expectedResult' => []
            ],
        ];
    }
}
