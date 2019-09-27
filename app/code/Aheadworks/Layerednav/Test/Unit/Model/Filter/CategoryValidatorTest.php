<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter;

use Aheadworks\Layerednav\Model\Filter\CategoryValidator;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Category as CategoryModel;

/**
 * Test for \Aheadworks\Layerednav\Model\Config\Source\CategoryValidator
 */
class CategoryValidatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CategoryValidator
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(
            CategoryValidator::class,
            []
        );
    }

    /**
     * Test validate method if category mode is all
     */
    public function testValidateCategoryModeAll()
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getCategoryMode')
            ->willReturn(FilterInterface::CATEGORY_MODE_ALL);

        $categoryModel = $this->getMockBuilder(CategoryModel::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($this->model->validate($filterMock, $categoryModel));
    }

    /**
     * Test validate method if category mode is lowest level
     *
     * @param bool $hasChildren
     * @param bool $result
     * @dataProvider hasChildrenDataProvider
     */
    public function testValidateCategoryModeLowestLevel($hasChildren, $result)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getCategoryMode')
            ->willReturn(FilterInterface::CATEGORY_MODE_LOWEST_LEVEL);

        $categoryModel = $this->getMockBuilder(CategoryModel::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryModel->expects($this->once())
            ->method('hasChildren')
            ->willReturn($hasChildren);

        $this->assertEquals($result, $this->model->validate($filterMock, $categoryModel));
    }

    /**
     * @return array
     */
    public function hasChildrenDataProvider()
    {
        return [
            [true, false],
            [false, true],
        ];
    }

    /**
     * Test validate method if category mode is lowest level
     *
     * @param int $categoryId
     * @param int[] $excludeCategoryIds
     * @param bool $result
     * @dataProvider categoryExcludeDataProvider
     */
    public function testValidateCategoryModeExclude($categoryId, $excludeCategoryIds, $result)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getCategoryMode')
            ->willReturn(FilterInterface::CATEGORY_MODE_EXCLUDE);
        $filterMock->expects($this->atLeastOnce())
            ->method('getExcludeCategoryIds')
            ->willReturn($excludeCategoryIds);

        $categoryModel = $this->getMockBuilder(CategoryModel::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $categoryModel->expects($this->any())
            ->method('getId')
            ->willReturn($categoryId);

        $this->assertEquals($result, $this->model->validate($filterMock, $categoryModel));
    }

    /**
     * @return array
     */
    public function categoryExcludeDataProvider()
    {
        return [
            [1, [1, 2, 3], false],
            [1, [2, 3],  true],
            [1, [], true],
            [1, null, true],
        ];
    }

    /**
     * Test validate method if category mode is not set
     *
     * @param int|null $categoryMode
     * @dataProvider categoryModeDataProvider
     */
    public function testValidateCategoryModeNotSet($categoryMode)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getCategoryMode')
            ->willReturn($categoryMode);

        $categoryModel = $this->getMockBuilder(CategoryModel::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertTrue($this->model->validate($filterMock, $categoryModel));
    }

    /**
     * @return array
     */
    public function categoryModeDataProvider()
    {
        return [[0], [null],];
    }
}
