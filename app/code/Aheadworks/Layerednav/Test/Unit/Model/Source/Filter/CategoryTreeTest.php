<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Source\Filter;

use Aheadworks\Layerednav\Model\Source\Filter\CategoryTree;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

/**
 * Test for \Aheadworks\Layerednav\Model\Source\Filter\CategoryTree
 */
class CategoryTreeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CategoryTree
     */
    private $model;

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

        $this->categoryCollectionFactoryMock = $this->getMockBuilder(CategoryCollectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            CategoryTree::class,
            [
                'categoryCollectionFactory' => $this->categoryCollectionFactoryMock
            ]
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $categoryOne = [
            'id' => 2,
            'name' => 'Category 1',
            'is_active' => 1,
            'parent_id' => CategoryModel::TREE_ROOT_ID
        ];
        $categoryTwo = [
            'id' => 3,
            'name' => 'Category 2',
            'is_active' => 1,
            'parent_id' => $categoryOne['id']
        ];

        $result = [
            [
                'value' => $categoryOne['id'],
                'is_active' => $categoryOne['is_active'],
                'label' =>  $categoryOne['name'],
                'optgroup' => [
                    [
                        'value' => $categoryTwo['id'],
                        'is_active' => $categoryTwo['is_active'],
                        'label' =>  $categoryTwo['name'],
                    ]
                ]
            ]
        ];

        $categoryCollectionMock = $this->getMockBuilder(CategoryCollection::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->categoryCollectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($categoryCollectionMock);

        $categoryCollectionMock->expects($this->once())
            ->method('addAttributeToSelect')
            ->with(['name', 'is_active', 'parent_id'])
            ->willReturnSelf();

        $categoryOneMock = $this->getCategoryMock($categoryOne);
        $categoryTwoMock = $this->getCategoryMock($categoryTwo);

        $categoryCollectionMock->expects($this->once())
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$categoryOneMock, $categoryTwoMock]));

        $this->assertEquals($result, $this->model->toOptionArray());
    }

    /**
     * Get category mock
     *
     * @param array $categoryData
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getCategoryMock($categoryData)
    {
        $categoryMock = $this->getMockBuilder(CategoryModel::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($categoryData['id']);
        $categoryMock->expects($this->once())
            ->method('getName')
            ->willReturn($categoryData['name']);
        $categoryMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($categoryData['is_active']);
        $categoryMock->expects($this->atLeastOnce())
            ->method('getParentId')
            ->willReturn($categoryData['parent_id']);

        return $categoryMock;
    }
}
