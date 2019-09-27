<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Category;

use Aheadworks\Layerednav\Model\Category\Resolver;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Category\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * @var CategoryRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryRepositoryMock;

    /**
     * @var CategoryCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryCollectionFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->categoryRepositoryMock = $this->createMock(CategoryRepositoryInterface::class);
        $this->categoryCollectionFactoryMock = $this->createMock(CategoryCollectionFactory::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'categoryRepository' => $this->categoryRepositoryMock,
                'categoryCollectionFactory' => $this->categoryCollectionFactoryMock,
            ]
        );
    }

    /**
     * Test testGetById method
     */
    public function testGetById()
    {
        $categoryId = 125;
        $categoryMock = $this->createMock(CategoryInterface::class);
        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with($categoryId)
            ->willReturn($categoryMock);

        $this->assertSame($categoryMock, $this->model->getById($categoryId));
    }

    /**
     * Test testGetById method if no category found
     */
    public function testGetByIdNoCategory()
    {
        $categoryId = 125;
        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with($categoryId)
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->assertNull($this->model->getById($categoryId));
    }

    /**
     * Test getCategoryName method
     */
    public function testGetCategoryName()
    {
        $categoryId = 125;
        $categoryName = 'Category Name';

        $categoryMock = $this->createMock(CategoryInterface::class);
        $categoryMock->expects($this->once())
            ->method('getName')
            ->willReturn($categoryName);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with($categoryId)
            ->willReturn($categoryMock);

        $this->assertEquals($categoryName, $this->model->getCategoryName($categoryId));
    }

    /**
     * Test getCategoryName method if no category found
     */
    public function testGetCategoryNameNoCategory()
    {
        $categoryId = 125;
        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with($categoryId)
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->assertEquals('', $this->model->getCategoryName($categoryId));
    }

    /**
     * Test getByUrlKey method
     */
    public function testGetByUrlKey()
    {
        $categoryUrlKey = 'url-key';
        $categoryId = 125;

        $categoryModelMock = $this->createMock(CategoryModel::class);
        $categoryModelMock->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($categoryId);

        $categoryCollectionMock = $this->createMock(CategoryCollection::class);
        $categoryCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with('url_key', $categoryUrlKey)
            ->willReturnSelf();
        $categoryCollectionMock->expects($this->once())
            ->method('addUrlRewriteToResult')
            ->willReturnSelf();
        $categoryCollectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($categoryModelMock);
        $this->categoryCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($categoryCollectionMock);

        $categoryMock = $this->createMock(CategoryInterface::class);
        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with($categoryId)
            ->willReturn($categoryMock);

        $this->assertSame($categoryMock, $this->model->getByUrlKey($categoryUrlKey));
    }

    /**
     * Test getByUrlKey method if no category found
     */
    public function testGetByUrlKeyNoCategoryFound()
    {
        $categoryUrlKey = 'url-key';
        $categoryId = null;

        $categoryModelMock = $this->createMock(CategoryModel::class);
        $categoryModelMock->expects($this->once())
            ->method('getCategoryId')
            ->willReturn($categoryId);

        $categoryCollectionMock = $this->createMock(CategoryCollection::class);
        $categoryCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with('url_key', $categoryUrlKey)
            ->willReturnSelf();
        $categoryCollectionMock->expects($this->once())
            ->method('addUrlRewriteToResult')
            ->willReturnSelf();
        $categoryCollectionMock->expects($this->once())
            ->method('getFirstItem')
            ->willReturn($categoryModelMock);
        $this->categoryCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($categoryCollectionMock);

        $this->categoryRepositoryMock->expects($this->once())
            ->method('get')
            ->with($categoryId)
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->assertNull($this->model->getByUrlKey($categoryUrlKey));
    }

    /**
     * Test getByUrlKey method with exception
     */
    public function testGetByUrlKeyException()
    {
        $categoryUrlKey = 'url-key';

        $categoryCollectionMock = $this->createMock(CategoryCollection::class);
        $categoryCollectionMock->expects($this->once())
            ->method('addAttributeToFilter')
            ->with('url_key', $categoryUrlKey)
            ->willThrowException(new LocalizedException(__("Error!")));
        $categoryCollectionMock->expects($this->never())
            ->method('addUrlRewriteToResult');
        $categoryCollectionMock->expects($this->never())
            ->method('getFirstItem');
        $this->categoryCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($categoryCollectionMock);

        $this->categoryRepositoryMock->expects($this->never())
            ->method('get');

        $this->assertNull($this->model->getByUrlKey($categoryUrlKey));
    }

    /**
     * Test getActiveCategoryIds method
     *
     * @param int[] $ids
     * @param int[]|false $expectedResult
     * @dataProvider getActiveCategoryIdsDataProvider
     * @throws \ReflectionException
     */
    public function testGetActiveCategoryIds($ids, $expectedResult)
    {
        $storeId = 3;

        $categoryMap = [
            [1, 3, $this->getCategoryMock(1, 0, 0, true)],
            [2, 3, $this->getCategoryMock(2, 1, 1, true)],
            [120, 3, $this->getCategoryMock(120, 2, 2, true)],
            [121, 3, $this->getCategoryMock(121, 2, 2, false)],
            [130, 3, $this->getCategoryMock(130, 120, 3, true)],
            [131, 3, $this->getCategoryMock(131, 121, 3, true)]
        ];
        $this->categoryRepositoryMock->expects($this->any())
            ->method('get')
            ->willReturnMap($categoryMap);

        $this->assertEquals($expectedResult, $this->model->getActiveCategoryIds($ids, $storeId));
    }

    /**
     * @return array
     */
    public function getActiveCategoryIdsDataProvider()
    {
        return [
            [
                'ids' => [],
                'expectedResult' => false
            ],
            [
                'ids' => [1],
                'expectedResult' => [1]
            ],
            [
                'ids' => [1, 2, 120, 131, 130],
                'expectedResult' => [1, 2, 120, 130]
            ],
            [
                'ids' => [1, 2, 120, 130],
                'expectedResult' => [1, 2, 120, 130]
            ],
            [
                'ids' => [131],
                'expectedResult' => false
            ],
        ];
    }

    /**
     * Test getActiveCategoryIds method if no category found
     */
    public function testGetActiveCategoryIdsNoCategoryFound()
    {
        $storeId = 3;
        $notFoundId = 9999;
        $categoryIds = [$notFoundId];

        $this->categoryRepositoryMock->expects($this->any())
            ->method('get')
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->assertFalse($this->model->getActiveCategoryIds($categoryIds, $storeId));
    }

    /**
     * Test getCategoryUrlKeys method
     *
     * @param array $ids
     * @param array $categoryMap
     * @param string[] $expectedResult
     * @dataProvider getCategoryUrlKeysDataProvider
     * @throws \ReflectionException
     */
    public function testGetCategoryUrlKeys($ids, $categoryMap, $expectedResult)
    {
        $this->categoryRepositoryMock->expects($this->atLeastOnce())
            ->method('get')
            ->willReturnMap($categoryMap);

        $this->assertEquals($expectedResult, $this->model->getCategoryUrlKeys($ids));
    }

    /**
     * @return array
     */
    public function getCategoryUrlKeysDataProvider()
    {
        return [
            [
                'ids' => [125, 126],
                'categoryMap' => [
                    [125, null, $this->getCategoryModelMock('url-key-1')],
                    [126, null, $this->getCategoryModelMock('url-key-2')]
                ],
                'expectedResult' => [
                    'url-key-1',
                    'url-key-2'
                ]
            ],
            [
                'ids' => [125, 'url-key-126'],
                'categoryMap' => [
                    [125, null, $this->getCategoryModelMock('url-key-1')],
                    [126, null, $this->getCategoryModelMock('url-key-2')]
                ],
                'expectedResult' => [
                    'url-key-1',
                    'url-key-126'
                ]
            ]
        ];
    }

    /**
     * Test getCategoryUrlKeys method if no category found
     */
    public function testGetCategoryUrlKeysNoCategoryFound()
    {
        $ids = [125, 126];

        $this->categoryRepositoryMock->expects($this->at(0))
            ->method('get')
            ->with(125)
            ->willReturn($this->getCategoryModelMock('url-key-1'));

        $this->categoryRepositoryMock->expects($this->at(1))
            ->method('get')
            ->with(126)
            ->willThrowException(new NoSuchEntityException(__('No such entity!')));

        $this->assertEquals(['url-key-1'], $this->model->getCategoryUrlKeys($ids));
    }

    /**
     * Get category model mock
     *
     * @param string $urlKey
     * @return CategoryInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getCategoryModelMock($urlKey)
    {
        $categoryMock = $this->createMock(CategoryModel::class);
        $categoryMock->expects($this->any())
            ->method('getUrlKey')
            ->willReturn($urlKey);

        return $categoryMock;
    }

    /**
     * Get category mock
     *
     * @param int $id
     * @param int|null $parentId
     * @param int|null $level
     * @param bool|null $isActive
     * @return CategoryInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getCategoryMock($id, $parentId, $level, $isActive)
    {
        $categoryMock = $this->createMock(CategoryInterface::class);
        $categoryMock->expects($this->any())
            ->method('getId')
            ->willReturn($id);
        $categoryMock->expects($this->any())
            ->method('getParentId')
            ->willReturn($parentId);
        $categoryMock->expects($this->any())
            ->method('getLevel')
            ->willReturn($level);
        $categoryMock->expects($this->any())
            ->method('getIsActive')
            ->willReturn($isActive);

        return $categoryMock;
    }
}
