<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Image;

use Aheadworks\Layerednav\Model\Image\Repository;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Api\Data\ImageInterfaceFactory;
use Aheadworks\Layerednav\Model\Image as ImageModel;
use Aheadworks\Layerednav\Model\ResourceModel\Image\Collection as ImageCollection;
use Aheadworks\Layerednav\Model\ResourceModel\Image\CollectionFactory as ImageCollectionFactory;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Image\Repository
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
     * @var ImageInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageFactoryMock;

    /**
     * @var ImageCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageCollectionFactoryMock;

    /**
     * @var JoinProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $extensionAttributesJoinProcessorMock;

    /**
     * @var CollectionProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->entityManagerMock = $this->createMock(EntityManager::class);
        $this->imageFactoryMock = $this->createMock(ImageInterfaceFactory::class);
        $this->imageCollectionFactoryMock = $this->createMock(ImageCollectionFactory::class);
        $this->extensionAttributesJoinProcessorMock = $this->createMock(JoinProcessorInterface::class);
        $this->collectionProcessorMock = $this->createMock(CollectionProcessorInterface::class);

        $this->model = $objectManager->getObject(
            Repository::class,
            [
                'entityManager' => $this->entityManagerMock,
                'imageFactory' => $this->imageFactoryMock,
                'imageCollectionFactory' => $this->imageCollectionFactoryMock,
                'extensionAttributesJoinProcessor' => $this->extensionAttributesJoinProcessorMock,
                'collectionProcessor' => $this->collectionProcessorMock,
            ]
        );
    }

    /**
     * Test get method
     */
    public function testGet()
    {
        $imageId = 12;

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($imageMock, $imageId)
            ->willReturn($imageMock);

        $this->assertSame($imageMock, $this->model->get($imageId));
        $this->assertSame($imageMock, $this->model->get($imageId));
    }

    /**
     * Test get method with exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 12
     */
    public function testGetException()
    {
        $imageId = 12;

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($imageMock, $imageId)
            ->willReturn($imageMock);

        $this->model->get($imageId);
    }

    /**
     * Test get method with empty id
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id =
     */
    public function testGetExceptionEmptyId()
    {
        $imageId = null;

        $this->imageFactoryMock->expects($this->never())
            ->method('create');

        $this->entityManagerMock->expects($this->never())
            ->method('load');

        $this->model->get($imageId);
    }

    /**
     * Test save method
     */
    public function testSave()
    {
        $imageId = 12;
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($imageMock);

        $newImageMock = $this->createMock(ImageInterface::class);
        $newImageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newImageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($newImageMock, $imageId)
            ->willReturn($newImageMock);

        $this->assertSame($newImageMock, $this->model->save($imageMock));
    }

    /**
     * Test save method with exception on saving
     *
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     */
    public function testSaveExceptionOnSave()
    {
        $imageId = 12;
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($imageMock)
            ->willThrowException(new \Exception());

        $this->imageFactoryMock->expects($this->never())
            ->method('create');

        $this->entityManagerMock->expects($this->never())
            ->method('load');

        $this->model->save($imageMock);
    }

    /**
     * Test save method with exception on getting
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 12
     */
    public function testSaveExceptionOnGet()
    {
        $imageId = 12;
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->entityManagerMock->expects($this->once())
            ->method('save')
            ->with($imageMock);

        $newImageMock = $this->createMock(ImageInterface::class);
        $newImageMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newImageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($newImageMock, $imageId)
            ->willReturn($newImageMock);

        $this->model->save($imageMock);
    }

    /**
     * Test delete method
     */
    public function testDelete()
    {
        $imageId = 12;
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($imageMock);

        $this->assertTrue($this->model->delete($imageMock));
    }

    /**
     * Test delete method with exception
     *
     * @expectedException \Exception
     */
    public function testDeleteException()
    {
        $imageId = 12;
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($imageMock)
            ->willThrowException(new \Exception());

        $this->model->delete($imageMock);
    }

    /**
     * Test deleteById method
     */
    public function testDeleteById()
    {
        $imageId = 12;
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($imageMock, $imageId)
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($imageMock);

        $this->assertTrue($this->model->deleteById($imageId));
    }

    /**
     * Test deleteById method with exception on deleting
     *
     * @expectedException \Exception
     */
    public function testDeleteByIdException()
    {
        $imageId = 12;
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($imageMock, $imageId)
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('delete')
            ->with($imageMock)
            ->willThrowException(new \Exception());

        $this->model->deleteById($imageId);
    }

    /**
     * Test deleteById method with exception on fetching entity
     *
     * @expectedException \Exception
     */
    public function testDeleteByIdExceptionOnGet()
    {
        $imageId = 12;
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($imageMock, $imageId)
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->never())
            ->method('delete');

        $this->model->deleteById($imageId);
    }

    /**
     * Test getList method
     */
    public function testGetList()
    {
        $imageId = 10;

        $imageModelMock = $this->createMock(ImageModel::class);
        $imageModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $collectionMock = $this->createMock(ImageCollection::class);
        $this->imageCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, ImageInterface::class);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$imageModelMock]);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($imageMock, $imageId)
            ->willReturn($imageMock);

        $this->assertSame(
            [$imageMock],
            $this->model->getList($searchCriteriaMock)
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
        $imageId = 10;

        $imageModelMock = $this->createMock(ImageModel::class);
        $imageModelMock->expects($this->any())
            ->method('getId')
            ->willReturn($imageId);

        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getId')
            ->willReturn(null);

        $searchCriteriaMock = $this->createMock(SearchCriteriaInterface::class);

        $collectionMock = $this->createMock(ImageCollection::class);
        $this->imageCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $this->extensionAttributesJoinProcessorMock->expects($this->once())
            ->method('process')
            ->with($collectionMock, ImageInterface::class);

        $this->collectionProcessorMock->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);

        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$imageModelMock]);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageMock);

        $this->entityManagerMock->expects($this->once())
            ->method('load')
            ->with($imageMock, $imageId)
            ->willReturn($imageMock);

        $this->model->getList($searchCriteriaMock);
    }
}
