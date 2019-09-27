<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Attribute;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Attribute\OptionsPreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Attribute
 */
class AttributeTest extends TestCase
{
    /**
     * @var Attribute
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
            Attribute::class,
            [
                'filterDataResolver' => $this->filterDataResolverMock,
                'optionsPreparer' => $this->optionsPreparerMock,
                'itemDataBuilder' => $this->itemDataBuilderMock,
            ]
        );
    }

    /**
     * Test getItemsData method
     * @param bool $isFilterable
     * @dataProvider getItemsDataDataProvider
     * @throws \ReflectionException
     */
    public function testGetItemsData($isFilterable)
    {
        $options = ['attribute_options'];
        $optionsCounts = ['options_counts'];
        $itemData = [
            'label' => 'Test',
            'value' => 125,
            'count' => 5,
            'image' => []
        ];
        $itemsData = [$itemData];

        $attributeFrontendMock = $this->createMock(AbstractFrontend::class);
        $attributeFrontendMock->expects($this->once())
            ->method('getSelectOptions')
            ->willReturn($options);
        $attributeMock = $this->createMock(EavAttribute::class);
        $attributeMock->expects($this->once())
            ->method('getFrontend')
            ->willReturn($attributeFrontendMock);
        $attributeMock->expects($this->once())
            ->method('getIsFilterable')
            ->willReturn($isFilterable);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getAttributeModel')
            ->willReturn($attributeMock);

        $this->filterDataResolverMock->expects($this->once())
            ->method('getFacetedData')
            ->with($filterMock)
            ->willReturn($optionsCounts);

        $withCountOnly = (int)$isFilterable == Attribute::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS;
        $this->optionsPreparerMock->expects($this->once())
            ->method('perform')
            ->with($filterMock, $options, $optionsCounts, $withCountOnly)
            ->willReturn($itemsData);

        $this->itemDataBuilderMock->expects($this->once())
            ->method('addItemData')
            ->with('Test', 125, 5, [])
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
            ['isFilterable' => true],
            ['isFilterable' => false]
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
