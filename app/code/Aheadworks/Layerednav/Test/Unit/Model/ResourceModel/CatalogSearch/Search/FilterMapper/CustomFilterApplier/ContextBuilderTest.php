<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\Context;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\ContextFactory;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\ContextBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Test for
 * \Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\ContextBuilder
 */
class ContextBuilderTest extends TestCase
{
    /**
     * @var ContextBuilder
     */
    private $model;

    /**
     * @var ContextFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $contextFactoryMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var HttpContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpContextMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->contextFactoryMock = $this->createMock(ContextFactory::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->httpContextMock = $this->createMock(HttpContext::class);

        $this->model = $objectManager->getObject(
            ContextBuilder::class,
            [
                'contextFactory' => $this->contextFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'httpContext' => $this->httpContextMock
            ]
        );
    }

    /**
     * Test build method
     */
    public function testBuild()
    {
        $storeId = 3;
        $customerGroupId = 123;

        $contextMock = $this->getContextMock($storeId, $customerGroupId);
        $this->contextFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($contextMock);

        $this->storeManagerMock->expects($this->never())
            ->method('getStore');

        $this->httpContextMock->expects($this->never())
            ->method('getValue');

        $this->assertSame($contextMock, $this->model->build($storeId, $customerGroupId));
    }

    /**
     * Test build method if no parameters specified
     */
    public function testBuildNoParameters()
    {
        $storeId = 3;
        $customerGroupId = 123;

        $contextMock = $this->getContextMock($storeId, $customerGroupId);
        $this->contextFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($contextMock);

        $storeMock = $this->getStoreMock($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_GROUP)
            ->willReturn($customerGroupId);

        $this->assertSame($contextMock, $this->model->build());
    }

    /**
     * Test build method if no store found
     */
    public function testBuildNoStoreFound()
    {
        $storeId = Store::DEFAULT_STORE_ID;
        $customerGroupId = 123;

        $contextMock = $this->getContextMock($storeId, $customerGroupId);
        $this->contextFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($contextMock);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->httpContextMock->expects($this->once())
            ->method('getValue')
            ->with(CustomerContext::CONTEXT_GROUP)
            ->willReturn($customerGroupId);

        $this->assertSame($contextMock, $this->model->build());
    }

    /**
     * Get context mock
     *
     * @param int $storeId
     * @param int $customerGroupId
     * @return Context|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getContextMock($storeId, $customerGroupId)
    {
        $contextMock = $this->createMock(Context::class);
        $contextMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $contextMock->expects($this->once())
            ->method('setCustomerGroupId')
            ->with($customerGroupId)
            ->willReturnSelf();

        return $contextMock;
    }

    /**
     * Get store mock
     *
     * @param int $storeId
     * @return StoreInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getStoreMock($storeId)
    {
        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);

        return $storeMock;
    }
}
