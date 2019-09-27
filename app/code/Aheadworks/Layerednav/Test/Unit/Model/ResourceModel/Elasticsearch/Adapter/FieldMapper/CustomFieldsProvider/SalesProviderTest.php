<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider\SalesProvider;
use Aheadworks\Layerednav\Model\Customer\GroupResolver as CustomerGroupResolver;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for SalesProvider
 */
class SalesProviderTest extends TestCase
{
    /**
     * @var SalesProvider
     */
    private $model;

    /**
     * @var CustomerGroupResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customerGroupResolverMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->customerGroupResolverMock = $this->createMock(CustomerGroupResolver::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->model = $objectManager->getObject(
            SalesProvider::class,
            [
                'customerGroupResolver' => $this->customerGroupResolverMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * Test getFields method
     */
    public function testGetFields()
    {
        $context = [];
        $customerGroupIds = [11, 12];
        $websiteId = 2;

        $this->customerGroupResolverMock->expects($this->once())
            ->method('getAllCustomerGroupIds')
            ->willReturn($customerGroupIds);

        $websiteMock = $this->getWebsiteMock($websiteId);
        $this->storeManagerMock->expects($this->once())
            ->method('getWebsites')
            ->willReturn([$websiteMock]);

        $this->setProperty('name', 'field');
        $this->setProperty('type', 'field_type');

        $expectedResult = [
            'field_11_2' => [
                'type' => 'field_type'
            ],
            'field_12_2' => [
                'type' => 'field_type'
            ],
        ];

        $this->assertEquals($expectedResult, $this->model->getFields($context));
    }

    /**
     * Get website mock
     *
     * @param $websiteId
     * @return WebsiteInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getWebsiteMock($websiteId)
    {
        $websiteMock = $this->createMock(WebsiteInterface::class);
        $websiteMock->expects($this->any())
            ->method('getId')
            ->willReturn($websiteId);

        return $websiteMock;
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
