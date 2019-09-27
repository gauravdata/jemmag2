<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Price;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Price\ConditionResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Interval;
use Aheadworks\Layerednav\Model\Layer\Filter\Interval\Resolver as IntervalResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item as FilterItem;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder as FilterItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State\Applier as LayerStateApplier;
use Magento\Catalog\Model\Layer\Filter\Price\Render as PriceRenderer;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Applier\Price
 */
class PriceTest extends TestCase
{
    /**
     * @var Price
     */
    private $model;

    /**
     * @var IntervalResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $intervalResolverMock;

    /**
     * @var LayerStateApplier|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerStateApplierMock;

    /**
     * @var FilterItemListBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemListBuilderMock;

    /**
     * @var PriceRenderer|\PHPUnit_Framework_MockObject_MockObject
     */
    private $priceRendererMock;

    /**
     * @var ConditionResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $conditionResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->intervalResolverMock = $this->createMock(IntervalResolver::class);
        $this->layerStateApplierMock = $this->createMock(LayerStateApplier::class);
        $this->itemListBuilderMock = $this->createMock(FilterItemListBuilder::class);
        $this->priceRendererMock = $this->createMock(PriceRenderer::class);
        $this->conditionResolverMock = $this->createMock(ConditionResolver::class);

        $this->model = $objectManager->getObject(
            Price::class,
            [
                'intervalResolver' => $this->intervalResolverMock,
                'layerStateApplier' => $this->layerStateApplierMock,
                'itemListBuilder' => $this->itemListBuilderMock,
                'priceRenderer' => $this->priceRendererMock,
                'conditionResolver' => $this->conditionResolverMock
            ]
        );
    }

    /**
     * Test apply method
     *
     * @param string $filterData
     * @param string $from
     * @param string $to
     * @param string $value
     * @param string $label
     * @dataProvider applyDataProvider
     * @throws \ReflectionException
     */
    public function testApply($filterData, $from, $to, $value, $label)
    {
        $paramCode = 'test';
        $fromToCondition = ['from', 'to'];

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($paramCode)
            ->willReturn($filterData);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($paramCode);

        $intervalMock = $this->getIntervalMock($from, $to);
        $this->intervalResolverMock->expects($this->once())
            ->method('getInterval')
            ->with($filterData)
            ->willReturn($intervalMock);

        $this->priceRendererMock->expects($this->once())
            ->method('renderRangeLabel')
            ->with(empty($from) ? 0 : $from, $to)
            ->willReturn($label);

        $this->conditionResolverMock->expects($this->once())
            ->method('getFromToCondition')
            ->with($from, $to)
            ->willReturn($fromToCondition);

        $filterItemMock = $this->createMock(FilterItem::class);
        $filterItems =  [$filterItemMock];

        $this->itemListBuilderMock->expects($this->once())
            ->method('add')
            ->with($filterMock, $label, $value, 0);
        $this->itemListBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterItems);

        $this->layerStateApplierMock->expects($this->once())
            ->method('add')
            ->with($filterItems, Price::PRICE_FIELD_NAME, $fromToCondition, true);

        $this->assertSame($this->model, $this->model->apply($requestMock, $filterMock));
    }

    /**
     * @return array
     */
    public function applyDataProvider()
    {
        return [
            [
                'filterData' => '10-25',
                'from' => '10',
                'to' => '25',
                'value' => '10-25',
                'label' => '$10.00-$24.99'
            ],
            [
                'filterData' => '-25',
                'from' => '',
                'to' => '25',
                'value' => '-25',
                'label' => '$0.00-$24.99'
            ],
        ];
    }

    /**
     * Test apply method if not valid filter data specified
     *
     * @param array|null|false|string $filterData
     * @param bool $intervalShouldBeChecked
     * @dataProvider applyNotValidFilterDataDataProvider
     * @throws \ReflectionException
     */
    public function testApplyNotValidFilterData($filterData, $intervalShouldBeChecked)
    {
        $paramCode = 'test';

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($paramCode)
            ->willReturn($filterData);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($paramCode);

        if ($intervalShouldBeChecked) {
            $this->intervalResolverMock->expects($this->once())
                ->method('getInterval')
                ->with($filterData)
                ->willReturn(false);
        } else {
            $this->intervalResolverMock->expects($this->never())
                ->method('getInterval');
        }

        $this->priceRendererMock->expects($this->never())
            ->method('renderRangeLabel');

        $this->conditionResolverMock->expects($this->never())
            ->method('getFromToCondition');

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
                'intervalShouldBeChecked' => false
            ],
            [
                'filterData' => null,
                'intervalShouldBeChecked' => false
            ],
            [
                'filterData' => 123,
                'intervalShouldBeChecked' => false
            ],
            [
                'filterData' => '123',
                'intervalShouldBeChecked' => true
            ],
        ];
    }

    /**
     * Get interval mock
     *
     * @param float $from
     * @param float $to
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getIntervalMock($from, $to)
    {
        $intervalMock = $this->createMock(Interval::class);
        $intervalMock->expects($this->any())
            ->method('getFrom')
            ->willReturn($from);
        $intervalMock->expects($this->any())
            ->method('getTo')
            ->willReturn($to);
        $intervalMock->expects($this->any())
            ->method('__toString')
            ->willReturn(implode('-', [$from, $to]));

        return $intervalMock;
    }
}
