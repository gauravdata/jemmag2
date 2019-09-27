<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\Swatch;

use Aheadworks\Layerednav\Model\Filter\Swatch\Repository;
use Magento\Store\Api\Data\StoreInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterfaceFactory;
use Aheadworks\Layerednav\Model\Filter\Swatch as SwatchModel;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\Collection as SwatchCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch\CollectionFactory as SwatchCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\Swatch\Repository
 */
class RepositoryTest extends TestCase
{
    /**
     * @var Repository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var SwatchInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $swatchFactoryMock;

    /**
     * @var SwatchCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $swatchCollectionFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

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

        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->swatchFactoryMock = $this->createMock(SwatchInterfaceFactory::class);
        $this->swatchCollectionFactoryMock = $this->createMock(SwatchCollectionFactory::class);
        $this->extensionAttributesJoinProcessorMock = $this->createMock(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->model = $objectManager->getObject(
            Repository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'swatchFactory' => $this->swatchFactoryMock,
                'swatchCollectionFactory' => $this->swatchCollectionFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'storeManager' => $this->storeManagerMock,
            ]
        );
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->assertSame($swatchMock, $this->model->get($swatchId, $storeId));
        $this->assertSame($swatchMock, $this->model->get($swatchId, $storeId));
    }

    /**
     * Test get method with no store specified
     */
    public function testGetNoStore()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->assertSame($swatchMock, $this->model->get($swatchId));
        $this->assertSame($swatchMock, $this->model->get($swatchId));
    }

    /**
     * Test get method with exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 12
     */
    public function testGetException()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->model->get($swatchId, $storeId);
    }

    /**
     * Test get method with exception with no store specified
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 12
     */
    public function testGetExceptionNoStore()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->model->get($swatchId);
    }

    /**
     * Test get method with exception on attempt to fetch current store
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetStoreException()
    {
        $swatchId = 12;

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $this->entityManagerMock->expects($this->never())
            ->method('load');

        $this->model->get($swatchId);
    }

    /**
     * Test save method with exception on saving
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveExceptionOnSave()
    {
        $storeId = 1;

        $swatchMock = $this->createMock(SwatchInterface::class);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($swatchMock)
            ->willThrowException(new \Exception());

        $this->storeManagerMock->expects($this->never())
            ->method('getStore');

        $this->entityManagerMock->expects($this->never())
            ->method('load');

        $this->model->save($swatchMock, $storeId);
    }

    /**
     * Test save method
     */
    public function testSave()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $originalSwatchMock = $this->createMock(SwatchInterface::class);
        $originalSwatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($originalSwatchMock)
            ->willReturn($originalSwatchMock);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->assertSame($swatchMock, $this->model->save($originalSwatchMock, $storeId));
    }

    /**
     * Test save method with no store specified
     */
    public function testSaveNoStore()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $originalSwatchMock = $this->createMock(SwatchInterface::class);
        $originalSwatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($originalSwatchMock)
            ->willReturn($originalSwatchMock);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->assertSame($swatchMock, $this->model->save($originalSwatchMock));
    }

    /**
     * Test save method with exception on attempt to fetch current store
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testSaveStoreException()
    {
        $swatchId = 12;

        $originalSwatchMock = $this->createMock(SwatchInterface::class);
        $originalSwatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($originalSwatchMock)
            ->willReturn($originalSwatchMock);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $this->entityManagerMock->expects($this->never())
            ->method('load');

        $this->assertSame($swatchMock, $this->model->save($originalSwatchMock));
    }

    /**
     * Test save method with exception on getting
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 12
     */
    public function testSaveExceptionOnGet()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $originalSwatchMock = $this->createMock(SwatchInterface::class);
        $originalSwatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($originalSwatchMock)
            ->willReturn($originalSwatchMock);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->assertSame($swatchMock, $this->model->save($originalSwatchMock, $storeId));
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {
        $swatchId = 12;
        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($swatchMock);

        $this->assertTrue($this->model->delete($swatchMock));
    }

    /**
     * Test deleteById method
     */
    public function testDeleteById()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($swatchMock);

        $this->assertTrue($this->model->deleteById($swatchId));
    }

    /**
     * Test deleteById method with exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 12
     */
    public function testDeleteByIdException()
    {
        $swatchId = 12;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->never())
            ->method('delete');

        $this->model->deleteById($swatchId);
    }

    /**
     * Test deleteById method with exception on attempt to fetch current store
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteByIdStoreException()
    {
        $swatchId = 12;

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $this->entityManagerMock->expects($this->never())
            ->method('load');

        $this->entityManagerMock->expects($this->never())
            ->method('delete');

        $this->model->deleteById($swatchId);
    }

    /**
     * Test getList method
     */
    public function testGetList()
    {
        $swatchId = 10;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $swatchModelMock = $this->createMock(SwatchModel::class);
        $swatchModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $collectionMock = $this->createMock(SwatchCollection::class);
        $this->swatchCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, SwatchInterface::class);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$swatchModelMock]);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->assertSame(
            [$swatchMock],
            $this->model->getList($searchCriteriaMock, $storeId)
        );
    }

    /**
     * Test getList method with exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 10
     */
    public function testGetListException()
    {
        $swatchId = 10;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $swatchModelMock = $this->createMock(SwatchModel::class);
        $swatchModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $collectionMock = $this->createMock(SwatchCollection::class);
        $this->swatchCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, SwatchInterface::class);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$swatchModelMock]);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->model->getList($searchCriteriaMock, $storeId);
    }

    /**
     * Test getList method when no store specified
     */
    public function testGetListNoStore()
    {
        $swatchId = 10;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $swatchModelMock = $this->createMock(SwatchModel::class);
        $swatchModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $collectionMock = $this->createMock(SwatchCollection::class);
        $this->swatchCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, SwatchInterface::class);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$swatchModelMock]);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->assertSame(
            [$swatchMock],
            $this->model->getList($searchCriteriaMock)
        );
    }

    /**
     * Test getList method with exception when no store specified
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 10
     */
    public function testGetListNoStoreException()
    {
        $swatchId = 10;
        $storeId = 1;
        $arguments = ['store_id' => $storeId];

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $swatchModelMock = $this->createMock(SwatchModel::class);
        $swatchModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $collectionMock = $this->createMock(SwatchCollection::class);
        $this->swatchCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, SwatchInterface::class);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$swatchModelMock]);

        $this->swatchFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($swatchMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($swatchMock, $swatchId, $arguments)
            ->willReturn($swatchMock);

        $this->model->getList($searchCriteriaMock);
    }

    /**
     * Test getList method when no store specified and store manager throws exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetListNoStoreExceptionStore()
    {
        $swatchId = 10;
        $storeId = 1;

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->any())
            ->method('getId')
            ->willReturn($storeId);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $swatchModelMock = $this->createMock(SwatchModel::class);
        $swatchModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $swatchMock = $this->createMock(SwatchInterface::class);
        $swatchMock->expects($this->any())
            ->method('getId')
            ->willReturn($swatchId);

        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $collectionMock = $this->createMock(SwatchCollection::class);
        $this->swatchCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, SwatchInterface::class);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $collectionMock->expects($this->never())
            ->method('getItems');

        $this->swatchFactoryMock->expects($this->never())
            ->method('create');

        $this->entityManagerMock->expects($this->never())
            ->method('load');

        $this->assertSame(
            [$swatchMock],
            $this->model->getList($searchCriteriaMock)
        );
    }
}
