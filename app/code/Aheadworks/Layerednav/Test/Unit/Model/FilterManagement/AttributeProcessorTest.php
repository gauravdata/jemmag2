<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\FilterManagement;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\FilterManagement\AttributeProcessor;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Store\Model\Store;

/**
 * Test for \Aheadworks\Layerednav\Model\FilterManagement
 */
class AttributeProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AttributeProcessor
     */
    private $model;

    /**
     * @var StoreValueInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeValueFactoryMock;

    /**
     * @var AttributeOptionLabelInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeOptionLabelFactoryMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->storeValueFactoryMock = $this->getMockBuilder(StoreValueInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeOptionLabelFactoryMock = $this->getMockBuilder(AttributeOptionLabelInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            AttributeProcessor::class,
            [
                'storeValueFactory' => $this->storeValueFactoryMock,
                'attributeOptionLabelFactory' => $this->attributeOptionLabelFactoryMock,
            ]
        );
    }

    /**
     * Test getStorefrontTitles method
     */
    public function testGetStorefrontTitles()
    {
        $storeId = 1;
        $label = "Attribute";
        $stareLabels = [
            $storeId => $label
        ];

        $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->setMethods(['getStoreLabels'])
            ->getMockForAbstractClass();
        $attributeMock->expects($this->once())
            ->method('getStoreLabels')
            ->willReturn($stareLabels);

        $titleValueMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $titleValueMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf();
        $titleValueMock->expects($this->once())
            ->method('setValue')
            ->with($label)
            ->willReturnSelf();

        $this->storeValueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($titleValueMock);

        $this->assertEquals([$titleValueMock], $this->model->getStorefrontTitles($attributeMock));
    }

    /**
     * Test getAttributeLabels method
     */
    public function testGetAttributeLabels()
    {
        $storeId = 1;
        $label = "Attribute";

        $titleValueMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $titleValueMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($storeId);
        $titleValueMock->expects($this->once())
            ->method('getValue')
            ->willReturn($label);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getStorefrontTitles')
            ->willReturn([$titleValueMock]);

        $attributeLabelMock = $this->getMockBuilder(AttributeOptionLabelInterface::class)
            ->getMockForAbstractClass();
        $attributeLabelMock->expects($this->once())
            ->method('setStoreId')
            ->with($storeId)
            ->willReturnSelf($storeId);
        $attributeLabelMock->expects($this->once())
            ->method('setLabel')
            ->with($label)
            ->willReturnSelf();

        $this->attributeOptionLabelFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributeLabelMock);

        $this->assertEquals([$attributeLabelMock], $this->model->getAttributeLabels($filterMock));
    }

    /**
     * Test isLabelsDifferent method
     *
     * @param array $attribute
     * @param array $filter
     * @dataProvider testIsLabelsDifferentDataProvider
     */
    public function testIsLabelsDifferent($attribute, $filter, $result)
    {
        $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->setMethods(['getStoreLabels'])
            ->getMockForAbstractClass();
        $attributeMock->expects($this->any())
            ->method('getDefaultFrontendLabel')
            ->willReturn($attribute['default_label']);
        $attributeMock->expects($this->once())
            ->method('getStoreLabels')
            ->willReturn($attribute['store_labels']);

        $storefrontTitleMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $storefrontTitleMock->expects($this->once())
            ->method('getStoreId')
            ->willReturn($filter['store_title']['store_id']);
        $storefrontTitleMock->expects($this->once())
            ->method('getValue')
            ->willReturn($filter['store_title']['value']);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getDefaultTitle')
            ->willReturn($filter['default_title']);
        $filterMock->expects($this->once())
            ->method('getStorefrontTitles')
            ->willReturn([$storefrontTitleMock]);

        $this->assertEquals($result, $this->model->isLabelsDifferent($attributeMock, $filterMock));
    }

    /**
     * @return array
     */
    public function testIsLabelsDifferentDataProvider()
    {
        return [
            [
                'attribute' => [
                    'default_label' => 'Color',
                    'store_labels' => [
                        1 => 'Color Store'
                    ]
                ],
                'filter' => [
                    'default_title' => 'Color',
                    'store_title' => [
                        'store_id' => 1,
                        'value' => 'Color Store'
                    ]
                ],
                false
            ],
            [
                'attribute' => [
                    'default_label' => 'Color',
                    'store_labels' => [
                        Store::DEFAULT_STORE_ID => 'Color',
                        1 => 'Color Store'
                    ]
                ],
                'filter' => [
                    'default_title' => 'Color',
                    'store_title' => [
                        'store_id' => 1,
                        'value' => 'Color Store'
                    ]
                ],
                false
            ],
            [
                'attribute' => [
                    'default_label' => 'Color',
                    'store_labels' => [
                        1 => 'Color Store'
                    ]
                ],
                'filter' => [
                    'default_title' => 'Color',
                    'store_title' => [
                        'store_id' => 1,
                        'value' => 'New Color Store'
                    ]
                ],
                true
            ]
        ];
    }
}
