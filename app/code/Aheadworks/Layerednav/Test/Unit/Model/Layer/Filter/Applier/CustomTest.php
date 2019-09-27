<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Custom;
use Aheadworks\Layerednav\Model\Layer\Filter\Item as FilterItem;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder as FilterItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State\Applier as LayerStateApplier;
use Aheadworks\Layerednav\Model\Seo\Checker as SeoChecker;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Applier\Custom
 */
class CustomTest extends TestCase
{
    /**
     * @var Custom
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
     * @var SeoChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $seoCheckerMock;

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
        $this->seoCheckerMock = $this->createMock(SeoChecker::class);

        $this->model = $objectManager->getObject(
            Custom::class,
            [
                'layerStateApplier' => $this->layerStateApplierMock,
                'itemListBuilder' => $this->itemListBuilderMock,
                'seoChecker' => $this->seoCheckerMock,
            ]
        );
    }

    /**
     * Test apply method
     *
     * @param bool $isNeedToUseTextValues
     * @dataProvider applyDataProvider
     * @throws \ReflectionException
     */
    public function testApply($isNeedToUseTextValues)
    {
        $paramCode = 'test';
        $filterData = FilterInterface::CUSTOM_FILTER_VALUE_YES;
        $title = 'Filter Title';
        $type = 'custom-type';

        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($paramCode)
            ->willReturn($filterData);

        $filterMock = $this->getFilterMock($paramCode, $title, $type);

        $this->seoCheckerMock->expects($this->once())
            ->method('isNeedToUseTextValues')
            ->willReturn($isNeedToUseTextValues);

        $value = $isNeedToUseTextValues ? $type : $filterData;

        $filterItemMock = $this->createMock(FilterItem::class);
        $filterItems =  [$filterItemMock];

        $this->itemListBuilderMock->expects($this->once())
            ->method('add')
            ->with($filterMock, $title, $value, 0)
            ->willReturnSelf();
        $this->itemListBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterItems);

        $this->layerStateApplierMock->expects($this->once())
            ->method('add')
            ->with($filterItems, $paramCode, [$filterData], false)
            ->willReturnSelf();

        $this->assertSame($this->model, $this->model->apply($requestMock, $filterMock));
    }

    /**
     * @return array
     */
    public function applyDataProvider()
    {
        return [
            ['isNeedToUseTextValues' => true],
            ['isNeedToUseTextValues' => false]
        ];
    }

    /**
     * Test apply method if no valid filter data provided
     *
     * @param string $paramCode
     * @param string|array $filterData
     * @dataProvider applyNotValidFilterValueDataProvider
     * @throws \ReflectionException
     */
    public function testApplyNotValidFilterValue($paramCode, $filterData)
    {
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($paramCode)
            ->willReturn($filterData);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($paramCode);

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
    public function applyNotValidFilterValueDataProvider()
    {
        return [
            [
                'paramCode' => 'test',
                'filterData' => FilterInterface::CUSTOM_FILTER_VALUE_NO
            ],
            [
                'paramCode' => 'test',
                'filterData' => []
            ],
            [
                'paramCode' => 'test',
                'filterData' => 123
            ],
            [
                'paramCode' => 'test',
                'filterData' => null
            ],
        ];
    }

    /**
     * Get filter mock
     *
     * @param string $code
     * @param string $title
     * @param string $type
     * @return FilterInterface|\PHPUnit_Framework_MockObject_MockObject
     * @throws \ReflectionException
     */
    private function getFilterMock($code, $title, $type)
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getCode')
            ->willReturn($code);
        $filterMock->expects($this->any())
            ->method('getTitle')
            ->willReturn($title);
        $filterMock->expects($this->any())
            ->method('getType')
            ->willReturn($type);

        return $filterMock;
    }
}
