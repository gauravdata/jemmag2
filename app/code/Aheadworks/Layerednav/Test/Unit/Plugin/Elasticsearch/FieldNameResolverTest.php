<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Plugin\Elasticsearch;

use Aheadworks\Layerednav\Plugin\Elasticsearch\FieldNameResolver;
use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolver
    as CustomFieldNameResolver;
use Magento\Customer\Model\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Psr\Log\LoggerInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeAdapter;

/**
 * Test for \Aheadworks\Layerednav\Plugin\Elasticsearch\FieldNameResolver
 */
class FieldNameResolverTest extends TestCase
{
    /**
     * @var FieldNameResolver
     */
    private $plugin;

    /**
     * @var CustomFieldNameResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customFieldNameResolverMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var HttpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContextMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     * @throws \ReflectionException
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->customFieldNameResolverMock = $this->createMock(CustomFieldNameResolver::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->httpContextMock = $this->createMock(HttpContext::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->plugin = $objectManager->getObject(
            FieldNameResolver::class,
            [
                'customFieldNameResolver' => $this->customFieldNameResolverMock,
                'storeManager' => $this->storeManagerMock,
                'httpContext' => $this->httpContextMock,
                'logger' => $this->loggerMock
            ]
        );
    }

    /**
     * Test aroundGetFieldName method
     *
     * @param string $code
     * @param bool $isCanBeProcessed
     * @dataProvider aroundGetFieldNameDataProvider
     */
    public function testAroundGetFieldName($code, $isCanBeProcessed)
    {
        $customFieldNameResolverMock = $this->createMock(CustomFieldNameResolver::class);
        $websiteId = 2;
        $customerGroupId = 11;

        $attributeAdapterMock = $this->getAttributeAdapterMock($code);
        $defaultContext = [];
        $extendedContext = [
            'website_id' => $websiteId,
            'customer_group_id' => $customerGroupId
        ];
        $result = 'processed-result';

        $this->customFieldNameResolverMock->expects($this->once())
            ->method('isCanBeProcessed')
            ->with($code)
            ->willReturn($isCanBeProcessed);

        if ($isCanBeProcessed) {
            $storeMock = $this->getStoreMock($websiteId);
            $this->storeManagerMock->expects($this->once())
                ->method('getStore')
                ->willReturn($storeMock);

            $this->httpContextMock->expects($this->once())
                ->method('getValue')
                ->with(Context::CONTEXT_GROUP)
                ->willReturn($customerGroupId);

            $this->customFieldNameResolverMock->expects($this->once())
                ->method('getFieldName')
                ->with($code, $extendedContext)
                ->willReturn($result);
        } else {
            $this->storeManagerMock->expects($this->never())
                ->method('getStore');

            $this->httpContextMock->expects($this->never())
                ->method('getValue');

            $this->customFieldNameResolverMock->expects($this->never())
                ->method('getFieldName');
        }

        $this->loggerMock->expects($this->never())
            ->method('critical');

        $proceed = function ($query) use ($attributeAdapterMock, $defaultContext, $result) {
            $this->assertEquals($attributeAdapterMock, $query);
            return $result;
        };

        $this->assertSame(
            $result,
            $this->plugin->aroundGetFieldName(
                $customFieldNameResolverMock,
                $proceed,
                $attributeAdapterMock,
                $defaultContext
            )
        );
    }

    /**
     * @return array
     */
    public function aroundGetFieldNameDataProvider()
    {
        return [
            [
                'code' => 'custom',
                'isCanBeProcessed' => false
            ],
            [
                'code' => 'custom',
                'isCanBeProcessed' => true
            ],
        ];
    }

    /**
     * Test aroundGetFieldName method if an error occurs
     */
    public function testAroundGetFieldNameException()
    {
        $customFieldNameResolverMock = $this->createMock(CustomFieldNameResolver::class);
        $websiteId = 2;
        $customerGroupId = 11;

        $code = 'custom';
        $attributeAdapterMock = $this->getAttributeAdapterMock($code);
        $defaultContext = [];
        $extendedContext = [
            'website_id' => $websiteId,
            'customer_group_id' => $customerGroupId
        ];
        $result = 'processed-result';

        $this->customFieldNameResolverMock->expects($this->once())
            ->method('isCanBeProcessed')
            ->with($code)
            ->willReturn(true);

        $storeMock = $this->getStoreMock($websiteId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(Context::CONTEXT_GROUP)
            ->willReturn($customerGroupId);

        $this->customFieldNameResolverMock->expects($this->once())
            ->method('getFieldName')
            ->with($code, $extendedContext)
            ->willThrowException(new \Exception('Error!'));

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with('Error!');

        $proceed = function ($query) use ($attributeAdapterMock, $defaultContext, $result) {
            $this->assertEquals($attributeAdapterMock, $query);
            return $result;
        };

        $this->assertSame(
            $result,
            $this->plugin->aroundGetFieldName(
                $customFieldNameResolverMock,
                $proceed,
                $attributeAdapterMock,
                $defaultContext
            )
        );
    }

    /**
     * Get store mock
     *
     * @param int $websiteId
     * @return StoreInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getStoreMock($websiteId)
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        return $storeMock;
    }

    /**
     * Get attribute adapter
     *
     * @param string $code
     * @return AttributeAdapter|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getAttributeAdapterMock($code)
    {
        $attributeAdapterMock = $this->createMock(AttributeAdapter::class);
        $attributeAdapterMock->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn($code);

        return $attributeAdapterMock;
    }
}
