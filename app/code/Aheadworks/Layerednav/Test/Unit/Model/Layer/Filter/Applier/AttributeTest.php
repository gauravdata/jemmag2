<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Applier;

use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Attribute;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Attribute\ValueResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemListBuilder as FilterItemListBuilder;
use Aheadworks\Layerednav\Model\Layer\Filter\Item as FilterItem;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State\Applier as LayerStateApplier;
use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Eav\Model\Entity;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Applier\Attribute
 */
class AttributeTest extends TestCase
{
    /**
     * @var Attribute
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
     * @var ValueResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $valueResolverMock;

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
        $this->valueResolverMock = $this->createMock(ValueResolver::class);

        $this->model = $objectManager->getObject(
            Attribute::class,
            [
                'layerStateApplier' => $this->layerStateApplierMock,
                'itemListBuilder' => $this->itemListBuilderMock,
                'valueResolver' => $this->valueResolverMock
            ]
        );
    }

    /**
     * Test apply method
     *
     * @param string $paramCode
     * @param string|array $filterData
     * @param string $attributeCode
     * @param array $optionMap
     * @param array $valueMap
     * @param array $expectedParams
     * @dataProvider applyDataProvider
     * @throws \ReflectionException
     */
    public function testApply($paramCode, $filterData, $attributeCode, $optionMap, $valueMap, $expectedParams)
    {
        $requestMock = $this->createMock(RequestInterface::class);
        $requestMock->expects($this->once())
            ->method('getParam')
            ->with($paramCode)
            ->willReturn($filterData);

        $frontendModel = $this->createMock(Entity::DEFAULT_FRONTEND_MODEL);
        $frontendModel->expects($this->atLeastOnce())
            ->method('getOption')
            ->willReturnMap($optionMap);

        $attributeMock = $this->createMock(EavAttribute::class);
        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($attributeCode);
        $attributeMock->expects($this->atLeastOnce())
            ->method('getFrontend')
            ->willReturn($frontendModel);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($paramCode);
        $filterMock->expects($this->atLeastOnce())
            ->method('getAttributeModel')
            ->willReturn($attributeMock);

        $this->valueResolverMock->expects($this->atLeastOnce())
            ->method('getValue')
            ->willReturnMap($valueMap);

        $filterItemMock = $this->createMock(FilterItem::class);
        $filterItems =  [$filterItemMock];

        $this->itemListBuilderMock->expects($this->atLeastOnce())
            ->method('add');

        $this->itemListBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($filterItems);

        $this->layerStateApplierMock->expects($this->once())
            ->method('add')
            ->with($filterItems, $attributeCode, $expectedParams, true);

        $this->assertSame($this->model, $this->model->apply($requestMock, $filterMock));
    }

    /**
     * @return array
     */
    public function applyDataProvider()
    {
        return [
            [
                'code' => 'test',
                'filterData' => '125',
                'attributeCode' => 'test-attr',
                'optionMap' => [
                    [125, 'label-125'],
                    [126, 'label-126']
                ],
                'valueMap' => [
                    ['label-125', 125, 'processed-label-125'],
                    ['label-126', 126, 'processed-label-126']
                ],
                'expectedParams' => [125]
            ],
            [
                'code' => 'test',
                'filterData' => '125,126',
                'attributeCode' => 'test-attr',
                'optionMap' => [
                    [125, 'label-125'],
                    [126, 'label-126']
                ],
                'valueMap' => [
                    ['label-125', 125, 'processed-label-125'],
                    ['label-126', 126, 'processed-label-126']
                ],
                'expectedParams' => [125, 126]
            ],
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
}
