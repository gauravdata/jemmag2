<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface as FilterCategoryDataInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface as FilterDataInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Checker;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface as FilterModeDataInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Aheadworks\Layerednav\Model\Layer\State\Item as StateItem;
use Aheadworks\Layerednav\Model\Layer\FilterInterface as LayerFilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Swatches\Helper\Data as SwatchesHelper;
use Aheadworks\Layerednav\Model\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Model\Source\Filter\SwatchesMode as FilterSwatchesMode;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Checker
 */
class CheckerTest extends TestCase
{
    /**
     * @var Checker
     */
    private $model;

    /**
     * @var Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $configMock;

    /**
     * @var LayerState|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerStateMock;

    /**
     * @var SwatchesHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $swatchesHelperMock;

    /**
     * @var FilterChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCheckerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->configMock = $this->createMock(Config::class);
        $this->layerStateMock = $this->createMock(LayerState::class);
        $this->swatchesHelperMock = $this->createMock(SwatchesHelper::class);
        $this->filterCheckerMock = $this->createMock(FilterChecker::class);

        $this->model = $objectManager->getObject(
            Checker::class,
            [
                'config' => $this->configMock,
                'layerState' => $this->layerStateMock,
                'swatchesHelper' => $this->swatchesHelperMock,
                'filterChecker' => $this->filterCheckerMock,
            ]
        );
    }

    /**
     * Test isMultiselectAvailable method
     *
     * @param string|null $filterMode
     * @param bool $expectedResult
     * @dataProvider isMultiselectAvailableDataProvider
     * @throws \ReflectionException
     */
    public function testIsMultiselectAvailable($filterMode, $expectedResult)
    {
        $layerFilterMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterMock->expects($this->once())
            ->method('getAdditionalData')
            ->with(FilterModeDataInterface::STOREFRONT_FILTER_MODE)
            ->willReturn($filterMode);

        $this->assertEquals($expectedResult, $this->model->isMultiselectAvailable($layerFilterMock));
    }

    /**
     * @return array
     */
    public function isMultiselectAvailableDataProvider()
    {
        return [
            [
                'filterMode' => null,
                'expectedResult' => false
            ],
            [
                'filterMode' => FilterModeDataInterface::MODE_SINGLE_SELECT,
                'expectedResult' => false
            ],
            [
                'filterMode' => FilterModeDataInterface::MODE_MULTI_SELECT,
                'expectedResult' => true
            ],
        ];
    }

    /**
     * Test isNeedToDisplay method
     *
     * @param FilterItemInterface[] $items
     * @param bool $hideEmptyFilters
     * @param bool $expectedResult
     * @dataProvider isNeedToDisplayDataProvider
     * @throws \ReflectionException
     */
    public function testIsNeedToDisplay($items, $hideEmptyFilters, $expectedResult)
    {
        $layerFilterMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->configMock->expects($this->any())
            ->method('hideEmptyFilters')
            ->willReturn($hideEmptyFilters);

        $this->assertEquals($expectedResult, $this->model->isNeedToDisplay($layerFilterMock));
    }

    /**
     * @return array
     */
    public function isNeedToDisplayDataProvider()
    {
        $filterItemWithCountMock = $this->createMock(FilterItemInterface::class);
        $filterItemWithCountMock->expects($this->any())
            ->method('getCount')
            ->willReturn(5);
        $filterItemWithoutCountMock = $this->createMock(FilterItemInterface::class);
        $filterItemWithoutCountMock->expects($this->any())
            ->method('getCount')
            ->willReturn(0);

        return [
            [
                'items' => [],
                'hideEmptyFilters' => true,
                'expectedResult' => false
            ],
            [
                'items' => [],
                'hideEmptyFilters' => false,
                'expectedResult' => false
            ],
            [
                'items' => [$filterItemWithCountMock],
                'hideEmptyFilters' => true,
                'expectedResult' => true
            ],
            [
                'items' => [$filterItemWithCountMock],
                'hideEmptyFilters' => false,
                'expectedResult' => true
            ],
            [
                'items' => [$filterItemWithoutCountMock],
                'hideEmptyFilters' => true,
                'expectedResult' => false
            ],
            [
                'items' => [$filterItemWithoutCountMock],
                'hideEmptyFilters' => false,
                'expectedResult' => true
            ],
            [
                'items' => [$filterItemWithCountMock, $filterItemWithoutCountMock],
                'hideEmptyFilters' => true,
                'expectedResult' => true
            ],
        ];
    }

    /**
     * Test isActive method
     *
     * @param FilterItemInterface[] $items
     * @param string $filterCode
     * @param bool $expectedResult
     * @dataProvider isActiveDataProvider
     * @throws \ReflectionException
     */
    public function testIsActive($items, $filterCode, $expectedResult)
    {
        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $layerFilterMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterMock->expects($this->any())
            ->method('getCode')
            ->willReturn($filterCode);

        $this->assertEquals($expectedResult, $this->model->isActive($layerFilterMock));
    }

    /**
     * @return array
     */
    public function isActiveDataProvider()
    {
        $activeCode = 'active-code';
        $inactiveCode = 'inactive-code';
        $stateItemMock = $this->getStateItemMock($activeCode);

        return [
            [
                'items' => [],
                'filterCode' => 'some-code',
                'expectedResult' => false
            ],
            [
                'items' => [$stateItemMock],
                'filterCode' => $activeCode,
                'expectedResult' => true
            ],
            [
                'items' => [$stateItemMock],
                'filterCode' => $inactiveCode,
                'expectedResult' => false
            ],
        ];
    }

    /**
     * Test isCategoryFilterActive method
     *
     * @param StateItem[] $items
     * @param bool $expectedResult
     * @dataProvider isCategoryFilterActiveDataProvider
     */
    public function testIsCategoryFilterActive($items, $expectedResult)
    {
        $this->layerStateMock->expects($this->once())
            ->method('getItems')
            ->willReturn($items);

        $this->assertEquals($expectedResult, $this->model->isCategoryFilterActive());
    }

    /**
     * @return array
     */
    public function isCategoryFilterActiveDataProvider()
    {
        return [
            [
                'items' => [],
                'expectedResult' => false
            ],
            [
                'items' => [$this->getStateItemMock('cat', 'value')],
                'expectedResult' => true
            ],
            [
                'items' => [$this->getStateItemMock('cat', null)],
                'expectedResult' => false
            ],
            [
                'items' => [$this->getStateItemMock('some-code', 'value')],
                'expectedResult' => false
            ],
            [
                'items' => [$this->getStateItemMock('some-code', null)],
                'expectedResult' => false
            ],
        ];
    }

    /**
     * Test isDisplayStateExpanded method
     *
     * @param int|null $displayState
     * @param bool $expectedResult
     * @dataProvider isDisplayStateExpandedDataProvider
     * @throws \ReflectionException
     */
    public function testIsDisplayStateExpanded($displayState, $expectedResult)
    {
        $layerFilterMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterMock->expects($this->once())
            ->method('getAdditionalData')
            ->with(FilterDataInterface::STOREFRONT_DISPLAY_STATE)
            ->willReturn($displayState);

        $this->assertEquals($expectedResult, $this->model->isDisplayStateExpanded($layerFilterMock));
    }

    /**
     * @return array
     */
    public function isDisplayStateExpandedDataProvider()
    {
        return [
            [
                'displayState' => null,
                'expectedResult' => false
            ],
            [
                'displayState' => FilterInterface::DISPLAY_STATE_COLLAPSED,
                'expectedResult' => false
            ],
            [
                'displayState' => FilterInterface::DISPLAY_STATE_EXPANDED,
                'expectedResult' => true
            ],
        ];
    }

    /**
     * Test isCategory method
     */
    public function testIsCategory()
    {
        $layerFilterCategoryMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterCategoryMock->expects($this->once())
            ->method('getCode')
            ->willReturn('cat');

        $layerFilterMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterMock->expects($this->once())
            ->method('getCode')
            ->willReturn('some-code');

        $this->assertTrue($this->model->isCategory($layerFilterCategoryMock));
        $this->assertFalse($this->model->isCategory($layerFilterMock));
    }

    /**
     * Test isSinglePathStyleAppliedForCategoryFilter method
     *
     * @param string $code
     * @param string $listStyle
     * @param bool $expectedResult
     * @dataProvider isSinglePathStyleAppliedForCategoryFilterDataProvider
     * @throws \ReflectionException
     */
    public function testIsSinglePathStyleAppliedForCategoryFilter($code, $listStyle, $expectedResult)
    {
        $layerFilterMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($code);

        $layerFilterMock->expects($this->any())
            ->method('getAdditionalData')
            ->with(FilterCategoryDataInterface::STOREFRONT_LIST_STYLE)
            ->willReturn($listStyle);

        $this->assertEquals($expectedResult, $this->model->isSinglePathStyleAppliedForCategoryFilter($layerFilterMock));
    }

    /**
     * @return array
     */
    public function isSinglePathStyleAppliedForCategoryFilterDataProvider()
    {
        return [
            [
                'code' => 'cat',
                'listStyle' => FilterCategoryInterface::CATEGORY_STYLE_DEFAULT,
                'expectedResult' => false
            ],
            [
                'code' => 'cat',
                'listStyle' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
                'expectedResult' => true
            ],
            [
                'code' => 'some-code',
                'listStyle' => FilterCategoryInterface::CATEGORY_STYLE_DEFAULT,
                'expectedResult' => false
            ],
            [
                'code' => 'some-code',
                'listStyle' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
                'expectedResult' => false
            ],
        ];
    }

    /**
     * Test isSwatchAttribute method
     *
     * @param Attribute|null $attributeModel
     * @param bool $isSwatchAttribute
     * @param bool $expectedResult
     * @dataProvider isSwatchAttributeDataProvider
     * @throws \ReflectionException
     */
    public function testIsSwatchAttribute($attributeModel, $isSwatchAttribute, $expectedResult)
    {
        $layerFilterMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterMock->expects($this->once())
            ->method('getAttributeModel')
            ->willReturn($attributeModel);

        $this->swatchesHelperMock->expects($this->any())
            ->method('isSwatchAttribute')
            ->with($attributeModel)
            ->willReturn($isSwatchAttribute);

        $this->assertEquals($expectedResult, $this->model->isSwatchAttribute($layerFilterMock));
    }

    /**
     * @return array
     */
    public function isSwatchAttributeDataProvider()
    {
        return [
            [
                'attributeModel' => $this->createMock(Attribute::class),
                'isSwatchAttribute' => false,
                'expectedResult' => false
            ],
            [
                'attributeModel' => $this->createMock(Attribute::class),
                'isSwatchAttribute' => true,
                'expectedResult' => true
            ],
            [
                'attributeModel' => null,
                'isSwatchAttribute' => null,
                'expectedResult' => false
            ],
        ];
    }

    /**
     * Test isPrice method
     *
     * @param Attribute|null $attributeModel
     * @param bool $expectedResult
     * @dataProvider isPriceDataProvider
     * @throws \ReflectionException
     */
    public function testIsPrice($attributeModel, $expectedResult)
    {
        $layerFilterMock = $this->createMock(LayerFilterInterface::class);
        $layerFilterMock->expects($this->once())
            ->method('getAttributeModel')
            ->willReturn($attributeModel);

        $this->assertEquals($expectedResult, $this->model->isPrice($layerFilterMock));
    }

    /**
     * @return array
     */
    public function isPriceDataProvider()
    {
        return [
            [
                'attributeModel' => $this->getAttributeMock(FilterInterface::PRICE_FILTER),
                'expectedResult' => true
            ],
            [
                'attributeModel' => $this->getAttributeMock(FilterInterface::SALES_FILTER),
                'expectedResult' => false
            ],
            [
                'attributeModel' => $this->getAttributeMock(FilterInterface::NEW_FILTER),
                'expectedResult' => false
            ],
            [
                'attributeModel' => $this->getAttributeMock(FilterInterface::STOCK_FILTER),
                'expectedResult' => false
            ],
            [
                'attributeModel' => $this->getAttributeMock('some-code'),
                'expectedResult' => false
            ],
        ];
    }

    /**
     * Test isNeedToShowFilterItemImage method
     */
    public function testIsNeedToShowFilterItemImage()
    {
        $isNeedToShowFilterItemImage = true;

        $swatchesViewMode = FilterSwatchesMode::IMAGE_AND_TITLE;
        $filterMock = $this->createMock(LayerFilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getAdditionalData')
            ->with(FilterInterface::SWATCHES_VIEW_MODE)
            ->willReturn($swatchesViewMode);
        $this->filterCheckerMock->expects($this->once())
            ->method('isNeedToShowSwatchImage')
            ->with($swatchesViewMode)
            ->willReturn($isNeedToShowFilterItemImage);

        $this->assertEquals($isNeedToShowFilterItemImage, $this->model->isNeedToShowFilterItemImage($filterMock));
    }

    /**
     * Test isNeedToShowFilterItemLabel method
     */
    public function testIsNeedToShowFilterItemLabel()
    {
        $isNeedToShowFilterItemLabel = true;

        $swatchesViewMode = FilterSwatchesMode::IMAGE_AND_TITLE;
        $filterMock = $this->createMock(LayerFilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getAdditionalData')
            ->with(FilterInterface::SWATCHES_VIEW_MODE)
            ->willReturn($swatchesViewMode);
        $this->filterCheckerMock->expects($this->once())
            ->method('isNeedToShowSwatchTitle')
            ->with($swatchesViewMode)
            ->willReturn($isNeedToShowFilterItemLabel);

        $this->assertEquals($isNeedToShowFilterItemLabel, $this->model->isNeedToShowFilterItemLabel($filterMock));
    }

    /**
     * Get attribute mock
     *
     * @param string $code
     * @return Attribute|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getAttributeMock($code)
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->any())
            ->method('getAttributeCode')
            ->willReturn($code);

        return $attributeMock;
    }

    /**
     * Get state item mock
     *
     * @param string $code
     * @param string|null $filterItemValue
     * @return StateItem|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getStateItemMock($code, $filterItemValue = null)
    {
        $filterMock = $this->createMock(LayerFilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getCode')
            ->willReturn($code);

        $filterItemMock = $this->createMock(FilterItemInterface::class);
        $filterItemMock->expects($this->any())
            ->method('getFilter')
            ->willReturn($filterMock);
        $filterItemMock->expects($this->any())
            ->method('getValue')
            ->willReturn($filterItemValue);

        $stateItemMock = $this->createMock(StateItem::class);
        $stateItemMock->expects($this->any())
            ->method('getFilterItem')
            ->willReturn($filterItemMock);

        return $stateItemMock;
    }
}
