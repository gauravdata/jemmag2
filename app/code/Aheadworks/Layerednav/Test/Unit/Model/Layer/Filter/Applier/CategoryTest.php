<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Category\Resolver as CategoryResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Category;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Category\FilterItemResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item as FilterItem;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder as FilterItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State\Applier as LayerStateApplier;
use Magento\Catalog\Model\Layer;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Applier\Category
 */
class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    private $model;

    /**
     * @var LayerStateApplier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerStateApplierMock;

    /**
     * @var FilterItemListBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemListBuilderMock;

    /**
     * @var CategoryResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoryResolverMock;

    /**
     * @var FilterItemResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterItemResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->layerStateApplierMock = $this->createMock(LayerStateApplier::class);
        $this->itemListBuilderMock = $this->createMock(FilterItemListBuilder::class);
        $this->categoryResolverMock = $this->createMock(CategoryResolver::class);
        $this->filterItemResolverMock = $this->createMock(FilterItemResolver::class);

        $this->model = $objectManager->getObject(
            Category::class,
            [
                'layerStateApplier' => $this->layerStateApplierMock,
                'itemListBuilder' => $this->itemListBuilderMock,
                'categoryResolver' => $this->categoryResolverMock,
                'filterItemResolver' => $this->filterItemResolverMock
            ]
        );
    }

    /**
     * Test apply method
     */
    public function testApply()
    {
        $paramCode = 'cat';
        $filterData = '127,128,129';
        $filterValues = [127,128,129];
        $activeCategoryIds = [127,129];

        $storeId = 3;
        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
            ->method('getId')
            ->willReturn($storeId);

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($paramCode)
            ->willReturn($filterData);

        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->once())
            ->method('getCurrentStore')
            ->willReturn($storeMock);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($paramCode);
        $filterMock->expects($this->once())
            ->method('getLayer')
            ->willReturn($layerMock);

        $this->categoryResolverMock->expects($this->atLeastOnce())
            ->method('getActiveCategoryIds')
            ->with($filterValues)
            ->willReturn($activeCategoryIds);

        $this->filterItemResolverMock->expects($this->atLeastOnce())
            ->method('getLabel')
            ->willReturnMap(
                [
                    [127, 'Category 127'],
                    [128, 'Category 128'],
                    [129, 'Category 129']
                ]
            );
        $this->filterItemResolverMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturnMap(
                [
                    [127, 'category-127'],
                    [128, 'category-128'],
                    [129, 'category-129']
                ]
            );

        $filterItemMock = $this->createMock(FilterItem::class);
        $filterItems =  [$filterItemMock];

        $this->itemListBuilderMock->expects($this->at(0))
            ->method('add')
            ->with($filterMock, 'Category 127', 'category-127', 0)
            ->willReturnSelf();
        $this->itemListBuilderMock->expects($this->at(1))
            ->method('add')
            ->with($filterMock, 'Category 129', 'category-129', 0)
            ->willReturnSelf();

        $this->itemListBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterItems);

        $this->layerStateApplierMock->expects($this->once())
            ->method('add')
            ->with($filterItems, Category::CATEGORY_FIELD_NAME, $activeCategoryIds, true);

        $this->assertSame($this->model, $this->model->apply($requestMock, $filterMock));
    }

    /**
     * Test apply method if not valid filter data specified
     *
     * @param array|null|false|string $filterData
     * @param bool $categoryShouldBeChecked
     * @dataProvider applyNotValidFilterDataDataProvider
     * @throws \ReflectionException
     */
    public function testApplyNotValidFilterData($filterData, $categoryShouldBeChecked)
    {
        $paramCode = 'cat';

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($paramCode)
            ->willReturn($filterData);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($paramCode);

        if ($categoryShouldBeChecked) {
            $storeId = 3;
            $storeMock = $this->createMock(Store::class);
            $storeMock->expects($this->once())
                ->method('getId')
                ->willReturn($storeId);

            $layerMock = $this->createMock(Layer::class);
            $layerMock->expects($this->once())
                ->method('getCurrentStore')
                ->willReturn($storeMock);

            $filterMock->expects($this->once())
                ->method('getLayer')
                ->willReturn($layerMock);

            $this->categoryResolverMock->expects($this->once())
                ->method('getActiveCategoryIds')
                ->willReturn(false);
        } else {
            $this->categoryResolverMock->expects($this->never())
                ->method('getActiveCategoryIds');
        }

        $this->itemListBuilderMock->expects($this->never())
            ->method('add');
        $this->itemListBuilderMock->expects($this->never())
            ->method('create');

        $this->layerStateApplierMock->expects($this->never())
            ->method('add');

        $this->assertSame($this->model, $this->model->apply($requestMock, $filterMock));
    }

    /**
     * @return array
     */
    public function applyNotValidFilterDataDataProvider()
    {
        return [
            [
                'filterData' => [],
                'categoryShouldBeChecked' => false
            ],
            [
                'filterData' => null,
                'categoryShouldBeChecked' => false
            ],
            [
                'filterData' => 123,
                'categoryShouldBeChecked' => false
            ],
            [
                'filterData' => '123',
                'categoryShouldBeChecked' => true
            ],
        ];
    }
}
