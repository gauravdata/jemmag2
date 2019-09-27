<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper;

use Aheadworks\Layerednav\Model\Customer\GroupResolver as CustomerGroupResolver;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper\SalesFieldsProvider;
use Aheadworks\Layerednav\Model\ResourceModel\Product\Collection\SalesProvider;
use Aheadworks\Layerednav\Model\Store\Resolver as StoreResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\BatchDataMapper\SalesFieldsProvider
 */
class SalesFieldsProviderTest extends TestCase
{
    /**
     * @var SalesFieldsProvider
     */
    private $model;

    /**
     * @var SalesProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    private $providerMock;

    /**
     * @var StoreResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeResolverMock;

    /**
     * @var CustomerGroupResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerGroupResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->providerMock = $this->createMock(SalesProvider::class);
        $this->storeResolverMock = $this->createMock(StoreResolver::class);
        $this->customerGroupResolverMock = $this->createMock(CustomerGroupResolver::class);

        $this->model = $objectManager->getObject(
            SalesFieldsProvider::class,
            [
                'provider' => $this->providerMock,
                'storeResolver' => $this->storeResolverMock,
                'customerGroupResolver' => $this->customerGroupResolverMock
            ]
        );
    }

    /**
     * Test getFields method
     *
     * @param int $websiteId
     * @param int[] $productIds
     * @param int[] $onSaleProducts
     * @param array $expectedResult
     * @dataProvider getFieldsDataProvider
     */
    public function testGetFields($websiteId, $productIds, $onSaleProducts, $expectedResult)
    {
        $storeId = 3;
        $customerGroupIds = [];
        $productsMap = [];
        foreach ($onSaleProducts as $customerGroupId => $groupProductIds) {
            $customerGroupIds[] = $customerGroupId;
            $productsMap[] = [true, $customerGroupId, $storeId, $groupProductIds];
        }

        $this->storeResolverMock->expects($this->once())
            ->method('getWebsiteIdByStoreId')
            ->with($storeId)
            ->willReturn($websiteId);
        $this->customerGroupResolverMock->expects($this->once())
            ->method('getAllCustomerGroupIds')
            ->willReturn($customerGroupIds);

        $this->providerMock->expects($this->any())
            ->method('getProductIds')
            ->willReturnMap($productsMap);

        $this->assertEquals($expectedResult, $this->model->getFields($productIds, $storeId));
    }

    /**
     * @return array
     */
    public function getFieldsDataProvider()
    {
        return [
            [
                'websiteId' => 2,
                'productIds' => [125, 126, 127, 6060],
                'onSaleProducts' => [
                    11 => [21, 23, 126, 5050],
                    12 => [21, 5050, 6060],
                ],
                'expectedResult' => [
                    125 => [
                        'aw_sales_11_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO,
                        'aw_sales_12_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO
                    ],
                    126 => [
                        'aw_sales_11_2' => FilterInterface::CUSTOM_FILTER_VALUE_YES,
                        'aw_sales_12_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO
                    ],
                    127 => [
                        'aw_sales_11_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO,
                        'aw_sales_12_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO
                    ],
                    6060 => [
                        'aw_sales_11_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO,
                        'aw_sales_12_2' => FilterInterface::CUSTOM_FILTER_VALUE_YES
                    ]
                ]
            ],
            [
                'websiteId' => 2,
                'productIds' => [125, 126, 127],
                'onSaleProducts' => [
                    11 => [],
                    12 => []
                ],
                'expectedResult' => [
                    125 => [
                        'aw_sales_11_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO,
                        'aw_sales_12_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO
                    ],
                    126 => [
                        'aw_sales_11_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO,
                        'aw_sales_12_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO
                    ],
                    127 => [
                        'aw_sales_11_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO,
                        'aw_sales_12_2' => FilterInterface::CUSTOM_FILTER_VALUE_NO
                    ]
                ]
            ],
            [
                'websiteId' => 2,
                'productIds' => [],
                'onSaleProducts' => [
                    11 => [21, 23, 126, 5050],
                    12 => [21, 5050, 6060],
                ],
                'expectedResult' => []
            ],
        ];
    }
}
