<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Custom;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Custom\OptionsPreparer;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Custom
 */
class CustomTest extends TestCase
{
    /**
     * @var Custom
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
            Custom::class,
            [
                'filterDataResolver' => $this->filterDataResolverMock,
                'optionsPreparer' => $this->optionsPreparerMock,
                'itemDataBuilder' => $this->itemDataBuilderMock,
            ]
        );
    }

    /**
     * Test getItemsData method
     */
    public function testGetItemsData()
    {
        $code = 'attr_code';
        $options = [
            [
                'value' => FilterInterface::CUSTOM_FILTER_VALUE_YES,
                'label' => __('Yes')
            ]
        ];
        $facets = [
            FilterInterface::CUSTOM_FILTER_VALUE_YES => [
                'value' => FilterInterface::CUSTOM_FILTER_VALUE_YES,
                'count' => 4
            ]
        ];
        $itemData = [
            'label' => 'Test',
            'value' => FilterInterface::CUSTOM_FILTER_VALUE_YES,
            'count' => 5,
            'image' => []
        ];
        $itemsData = [$itemData];

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($code);

        $this->filterDataResolverMock->expects($this->once())
            ->method('getFacetedData')
            ->with($filterMock, $code)
            ->willReturn($facets);

        $this->optionsPreparerMock->expects($this->once())
            ->method('perform')
            ->with($filterMock, $options, $facets, true)
            ->willReturn($itemsData);

        $this->itemDataBuilderMock->expects($this->once())
            ->method('addItemData')
            ->with('Test', FilterInterface::CUSTOM_FILTER_VALUE_YES, 5, [])
            ->willReturnSelf();
        $this->itemDataBuilderMock->expects($this->once())
            ->method('build')
            ->willReturn($itemsData);

        $this->assertSame($itemsData, $this->model->getItemsData($filterMock));
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
