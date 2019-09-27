<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Category;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Category\OptionsPreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Category
 */
class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    private $model;

    /**
     * @var FilterDataResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterDataResolverMock;

    /**
     * @var OptionsPreparer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $optionsPreparerMock;

    /**
     * @var DataBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemDataBuilderMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterDataResolverMock = $this->createMock(FilterDataResolver::class);
        $this->optionsPreparerMock = $this->createMock(OptionsPreparer::class);
        $this->itemDataBuilderMock = $this->createMock(DataBuilderInterface::class);

        $this->model = $objectManager->getObject(
            Category::class,
            [
                'filterDataResolver' => $this->filterDataResolverMock,
                'optionsPreparer' => $this->optionsPreparerMock,
                'itemDataBuilder' => $this->itemDataBuilderMock,
            ]
        );
    }

    /**
     * Test getItemsData method
     * @param bool $isActive
     * @dataProvider getItemsDataDataProvider
     * @throws \ReflectionException
     */
    public function testGetItemsData($isActive)
    {
        $optionsCounts = ['options_counts'];
        $itemData = [
            'label' => 'Test',
            'value' => 12,
            'count' => 5
        ];
        $itemsData = [$itemData];

        $categoryMock = $this->createMock(CategoryInterface::class);
        $categoryMock->expects($this->once())
            ->method('getIsActive')
            ->willReturn($isActive);

        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->once())
            ->method('getCurrentCategory')
            ->willReturn($categoryMock);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getLayer')
            ->willReturn($layerMock);

        $this->filterDataResolverMock->expects($this->any())
            ->method('getFacetedData')
            ->with($filterMock, Category::FILTER_FIELD_NAME)
            ->willReturn($optionsCounts);

        $this->optionsPreparerMock->expects($this->any())
            ->method('perform')
            ->with($categoryMock, $optionsCounts)
            ->willReturn($itemsData);

        $this->itemDataBuilderMock->expects($this->any())
            ->method('addItemData')
            ->with('Test', 12, 5)
            ->willReturnSelf();
        $this->itemDataBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($itemsData);

        $this->assertSame($itemsData, $this->model->getItemsData($filterMock));
    }

    /**
     * @return array
     */
    public function getItemsDataDataProvider()
    {
        return [
            ['isActive' => true],
            ['isActive' => false]
        ];
    }

    /**
     * Test getStatisticsData method
     */
    public function testGetStatisticsData()
    {
        $filterMock = $this->createMock(FilterInterface::class);

        $this->assertEquals([], $this->model->getStatisticsData($filterMock));
    }
}
