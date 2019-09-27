<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel;

use Aheadworks\Layerednav\Model\ResourceModel\FilterRepository;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterface;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterfaceFactory;
use Aheadworks\Layerednav\Model\Filter as FilterModel;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Collection as FilterCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\CollectionFactory as FilterCollectionFactory;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\FilterRepository
 */
class FilterRepositoryTest extends TestCase
{
    /**
     * @var FilterRepository
     */
    private $model;

    /**
     * @var EntityManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $entityManagerMock;

    /**
     * @var FilterInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterFactoryMock;

    /**
     * @var FilterCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCollectionFactoryMock;

    /**
     * @var FilterSearchResultsInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterSearchResultsFactoryMock;

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
        $this->filterFactoryMock = $this->createMock(FilterInterfaceFactory::class);
        $this->filterCollectionFactoryMock = $this->createMock(FilterCollectionFactory::class);
        $this->filterSearchResultsFactoryMock = $this->createMock(FilterSearchResultsInterfaceFactory::class);
        $this->extensionAttributesJoinProcessorMock = $this->createMock(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        $this->model = $objectManager->getObject(
            FilterRepository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'filterFactory' => $this->filterFactoryMock,
                'filterCollectionFactory' => $this->filterCollectionFactoryMock,
                'filterSearchResultsFactory' => $this->filterSearchResultsFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
                'storeManager' => $this->storeManagerMock
            ]
        );
    }

    /**
     * Test save method
     */
    public function testSave()
    {
        $filterId = 1;
        $storeId = 1;

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);
        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($filterMock)
            ->willReturn($filterMock);
        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->assertEquals($filterMock, $this->model->save($filterMock));
    }

    /**
     * Test save method if an error occurs
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage Unknown error!
     */
    public function testSaveException()
    {
        $filterMock = $this->createMock(FilterInterface::class);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($filterMock)
            ->willThrowException(new \Exception('Unknown error!'));

        $this->model->save($filterMock);
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $filterId = 1;
        $storeId = 1;

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->assertEquals($filterMock, $this->model->get($filterId));
    }

    /**
     * Test get method if specified filter does not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 2
     */
    public function testGetException()
    {
        $filterId = 2;
        $storeId = 1;

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->model->get($filterId);
    }

    /**
     * Test getByCode method
     */
    public function testGetByCode()
    {
        $filterId = 1;
        $filterCode = 'color';
        $filterType = FilterInterface::ATTRIBUTE_FILTER;
        $storeId = 1;

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $filterCollectionMock = $this->createMock(FilterCollection::class);
        $filterCollectionMock->expects($this->once())
            ->method('addFilterByCode')
            ->with($filterCode)
            ->willReturnSelf();
        $filterCollectionMock->expects($this->once())
            ->method('addFilterByType')
            ->with($filterType)
            ->willReturnSelf();
        $filterCollectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($filterMock);
        $this->filterCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterCollectionMock);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->assertEquals($filterMock, $this->model->getByCode($filterCode, $filterType));
    }

    /**
     * Test getByCode method if specified filter does not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with code = color
     */
    public function testGetByCodeException()
    {
        $filterCode = 'color';
        $filterType = FilterInterface::ATTRIBUTE_FILTER;

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $filterCollectionMock = $this->createMock(FilterCollection::class);
        $filterCollectionMock->expects($this->once())
            ->method('addFilterByCode')
            ->with($filterCode)
            ->willReturnSelf();
        $filterCollectionMock->expects($this->once())
            ->method('addFilterByType')
            ->with($filterType)
            ->willReturnSelf();
        $filterCollectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($filterMock);
        $this->filterCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterCollectionMock);

        $this->model->getByCode($filterCode, $filterType);
    }

    /**
     * Test getList method
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testGetList()
    {
        $collectionSize = 1;
        $storeId = 3;
        $filterId = 125;
        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->atLeastOnce())
            ->method('getStore')
            ->willReturn($storeMock);

        $collectionMock = $this->createMock(FilterCollection::class);
        $collectionMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $collectionMock->expects($this->once())
            ->method('getSize')
            ->willReturn($collectionSize);
        $this->filterCollectionFactoryMock
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, FilterInterface::class)
            ->willReturnSelf();

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock)
            ->willReturnSelf();

        $filterModelMock = $this->createMock(FilterModel::class);
        $filterModelMock->expects($this->once())
            ->method('getId')
            ->willReturn($filterId);
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$filterModelMock]);

        $filterToLoadMock = $this->createMock(FilterInterface::class);
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getId')
            ->willReturn($filterId);
        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterToLoadMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterToLoadMock, $filterId, ['store_id' => $storeId])
            ->willReturn($filterMock);

        $searchResultsMock = $this->createMock(FilterSearchResultsInterface::class);
        $searchResultsMock->expects($this->atLeastOnce())
            ->method('setSearchCriteria')
            ->with($searchCriteriaMock)
            ->willReturnSelf();
        $searchResultsMock->expects($this->once())
            ->method('setTotalCount')
            ->with($collectionSize);
        $searchResultsMock->expects($this->once())
            ->method('setItems')
            ->with([$filterMock])
            ->willReturnSelf();
        $this->filterSearchResultsFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($searchResultsMock);

        $this->assertSame($searchResultsMock, $this->model->getList($searchCriteriaMock));
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {
        $storeId = 1;
        $filterId = 2;

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($filterMock)
            ->willReturn(true);

        $this->assertTrue($this->model->delete($filterMock));
    }

    /**
     * Test delete method if specified filter does not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 2
     */
    public function testDeleteException()
    {
        $storeId = 1;
        $filterId = 2;

        $filterOneMock = $this->createMock(FilterInterface::class);
        $filterOneMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $filterTwoMock = $this->createMock(FilterInterface::class);
        $filterTwoMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterTwoMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterOneMock, $filterId)
            ->willReturn($filterTwoMock);

        $this->model->delete($filterOneMock);
    }

    /**
     * Test deleteById method
     */
    public function testDeleteById()
    {
        $storeId = 1;
        $filterId = 2;

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($filterId);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);
        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($filterMock)
            ->willReturn(true);

        $this->assertTrue($this->model->deleteById($filterId));
    }

    /**
     * Test deleteById method if specified filter does not exist
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 2
     */
    public function testDeleteByIdException()
    {
        $storeId = 1;
        $filterId = 2;

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn(null);

        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($filterMock);

        $storeMock = $this->createMock(StoreInterface::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($filterMock, $filterId)
            ->willReturn($filterMock);

        $this->model->deleteById($filterId);
    }
}
