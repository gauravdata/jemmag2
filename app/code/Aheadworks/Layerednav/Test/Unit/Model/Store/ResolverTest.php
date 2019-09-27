<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Store;

use Aheadworks\Layerednav\Model\Store\Resolver;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Test for \Aheadworks\Layerednav\Model\Store\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

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

        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test getWebsiteIdByStoreId method
     */
    public function testGetWebsiteIdByStoreId()
    {
        $websiteId = 2;
        $storeId = 3;

        $storeMock = $this->createMock(StoreInterface::class);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willReturn($storeMock);

        $storeMock->expects($this->once())
            ->method('getWebsiteId')
            ->willReturn($websiteId);

        $this->assertEquals($websiteId, $this->model->getWebsiteIdByStoreId($storeId));
    }

    /**
     * Test getWebsiteIdByStoreId method if no store found
     */
    public function testGetWebsiteIdByStoreIdNoStore()
    {
        $storeId = 3;

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->with($storeId)
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->assertNull($this->model->getWebsiteIdByStoreId($storeId));
    }

    /**
     * Test getStoresSortedBySortOrder method
     *
     * @param array $allStores
     * @param array $sortedStores
     * @dataProvider getStoresSortedBySortOrderDataProvider
     */
    public function testGetStoresSortedBySortOrder($allStores, $sortedStores)
    {
        $this->storeManagerMock->expects($this->once())
            ->method('getStores')
            ->with(true)
            ->willReturn($allStores);

        $this->assertEquals($sortedStores, $this->model->getStoresSortedBySortOrder($allStores));
    }

    public function getStoresSortedBySortOrderDataProvider()
    {
        $firstStore = $this->getStoreMock(1, 10);
        $secondStore = $this->getStoreMock(2, 10);
        $thirdStore = $this->getStoreMock(3, 2);
        return [
            [
                'allStores' => [],
                'sortedStores' => [],
            ],
            [
                'allStores' => [
                    $firstStore,
                ],
                'sortedStores' => [
                    $firstStore,
                ],
            ],
            [
                'allStores' => [
                    $firstStore,
                    $secondStore,
                    $thirdStore,
                ],
                'sortedStores' => [
                    $thirdStore,
                    $firstStore,
                    $secondStore
                ],
            ],
        ];
    }

    /**
     * Retrieve store model mock
     *
     * @param int $id
     * @param int $sortOrder
     * @return \PHPUnit\Framework\MockObject\MockObject|Store
     */
    private function getStoreMock($id, $sortOrder)
    {
        $storeMock = $this->createPartialMock(
            Store::class,
            [
                'getId',
                'getSortOrder',
            ]
        );
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $storeMock->expects($this->any())
            ->method('getSortOrder')
            ->willReturn($sortOrder);
        return $storeMock;
    }
}
