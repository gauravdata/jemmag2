<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\Swatch;

use Aheadworks\Layerednav\Model\Filter\Swatch\Converter;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Eav\Api\Data\AttributeOptionInterfaceFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory;
use Aheadworks\Layerednav\Model\StorefrontValueResolver;
use Magento\Store\Model\Store;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\Swatch\Converter
 */
class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    private $model;

    /**
     * @var AttributeOptionInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeOptionFactoryMock;

    /**
     * @var AttributeOptionLabelInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeOptionLabelFactoryMock;

    /**
     * @var StorefrontValueResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storefrontValueResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->attributeOptionFactoryMock = $this->createMock(AttributeOptionInterfaceFactory::class);
        $this->attributeOptionLabelFactoryMock = $this->createMock(AttributeOptionLabelInterfaceFactory::class);
        $this->storefrontValueResolverMock = $this->createMock(StorefrontValueResolver::class);

        $this->model = $objectManager->getObject(
            Converter::class,
            [
                'attributeOptionFactory' => $this->attributeOptionFactoryMock,
                'attributeOptionLabelFactory' => $this->attributeOptionLabelFactoryMock,
                'storefrontValueResolver' => $this->storefrontValueResolverMock,
            ]
        );
    }

    /**
     * Test toAttributeOption method
     */
    public function testToAttributeOption()
    {
        $actualCurrentStorefrontTitle = 'default';
        $defaultStorefrontTitle = $this->getStoreValueMock(Store::DEFAULT_STORE_ID, $actualCurrentStorefrontTitle);
        $storefrontTitle = $this->getStoreValueMock(1, 'title');
        $sortOrder = 2;
        $isDefault = false;
        $optionId = 12;
        $storefrontTitles = [
            $defaultStorefrontTitle,
            $storefrontTitle
        ];

        $filterSwatchItem = $this->createMock(SwatchInterface::class);
        $filterSwatchItem->expects($this->any())
            ->method('getStorefrontTitles')
            ->willReturn($storefrontTitles);
        $filterSwatchItem->expects($this->any())
            ->method('getSortOrder')
            ->willReturn($sortOrder);
        $filterSwatchItem->expects($this->any())
            ->method('getIsDefault')
            ->willReturn($isDefault);
        $filterSwatchItem->expects($this->any())
            ->method('getOptionId')
            ->willReturn($optionId);

        $this->storefrontValueResolverMock->expects($this->once())
            ->method('getStorefrontValue')
            ->with($storefrontTitles, Store::DEFAULT_STORE_ID)
            ->willReturn($actualCurrentStorefrontTitle);

        $attributeOptionMock = $this->createMock(Option::class);
        $attributeOptionMock->expects($this->once())
            ->method('setLabel')
            ->with($actualCurrentStorefrontTitle);
        $attributeOptionMock->expects($this->once())
            ->method('setSortOrder')
            ->with($sortOrder);
        $attributeOptionMock->expects($this->once())
            ->method('setIsDefault')
            ->with($isDefault);
        $attributeOptionMock->expects($this->once())
            ->method('setId')
            ->with($optionId);
        $attributeOptionMock->expects($this->once())
            ->method('setValue')
            ->with($optionId);

        $firstStoreLabel = $this->createMock(AttributeOptionLabelInterface::class);
        $firstStoreLabel->expects($this->once())
            ->method('setStoreId')
            ->with(Store::DEFAULT_STORE_ID);
        $firstStoreLabel->expects($this->once())
            ->method('setLabel')
            ->with($actualCurrentStorefrontTitle);

        $secondStoreLabel = $this->createMock(AttributeOptionLabelInterface::class);
        $secondStoreLabel->expects($this->once())
            ->method('setStoreId')
            ->with(1);
        $secondStoreLabel->expects($this->once())
            ->method('setLabel')
            ->with('title');

        $this->attributeOptionLabelFactoryMock->expects($this->at(0))
            ->method('create')
            ->willReturn($firstStoreLabel);
        $this->attributeOptionLabelFactoryMock->expects($this->at(1))
            ->method('create')
            ->willReturn($secondStoreLabel);

        $attributeOptionMock->expects($this->once())
            ->method('setStoreLabels')
            ->with([$firstStoreLabel, $secondStoreLabel]);

        $this->attributeOptionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributeOptionMock);

        $this->assertSame($attributeOptionMock, $this->model->toAttributeOption($filterSwatchItem));
    }

    /**
     * Test toSwatchAttributeOption method
     */
    public function testToSwatchAttributeOption()
    {
        $actualCurrentStorefrontTitle = 'default';
        $defaultStorefrontTitle = $this->getStoreValueMock(Store::DEFAULT_STORE_ID, $actualCurrentStorefrontTitle);
        $storefrontTitle = $this->getStoreValueMock(1, 'title');
        $sortOrder = 2;
        $isDefault = false;
        $optionId = 12;
        $storefrontTitles = [
            $defaultStorefrontTitle,
            $storefrontTitle
        ];
        $value = 'value';

        $filterSwatchItem = $this->createMock(SwatchInterface::class);
        $filterSwatchItem->expects($this->any())
            ->method('getStorefrontTitles')
            ->willReturn($storefrontTitles);
        $filterSwatchItem->expects($this->any())
            ->method('getSortOrder')
            ->willReturn($sortOrder);
        $filterSwatchItem->expects($this->any())
            ->method('getIsDefault')
            ->willReturn($isDefault);
        $filterSwatchItem->expects($this->any())
            ->method('getOptionId')
            ->willReturn($optionId);
        $filterSwatchItem->expects($this->any())
            ->method('getValue')
            ->willReturn($value);

        $this->storefrontValueResolverMock->expects($this->once())
            ->method('getStorefrontValue')
            ->with($storefrontTitles, Store::DEFAULT_STORE_ID)
            ->willReturn($actualCurrentStorefrontTitle);

        $attributeOptionMock = $this->createMock(Option::class);
        $attributeOptionMock->expects($this->once())
            ->method('setLabel')
            ->with($actualCurrentStorefrontTitle);
        $attributeOptionMock->expects($this->once())
            ->method('setSortOrder')
            ->with($sortOrder);
        $attributeOptionMock->expects($this->once())
            ->method('setIsDefault')
            ->with($isDefault);
        $attributeOptionMock->expects($this->once())
            ->method('setId')
            ->with($optionId);
        $attributeOptionMock->expects($this->once())
            ->method('setValue')
            ->with($value);

        $firstStoreLabel = $this->createMock(AttributeOptionLabelInterface::class);
        $firstStoreLabel->expects($this->once())
            ->method('setStoreId')
            ->with(Store::DEFAULT_STORE_ID);
        $firstStoreLabel->expects($this->once())
            ->method('setLabel')
            ->with($actualCurrentStorefrontTitle);

        $secondStoreLabel = $this->createMock(AttributeOptionLabelInterface::class);
        $secondStoreLabel->expects($this->once())
            ->method('setStoreId')
            ->with(1);
        $secondStoreLabel->expects($this->once())
            ->method('setLabel')
            ->with('title');

        $this->attributeOptionLabelFactoryMock->expects($this->at(0))
            ->method('create')
            ->willReturn($firstStoreLabel);
        $this->attributeOptionLabelFactoryMock->expects($this->at(1))
            ->method('create')
            ->willReturn($secondStoreLabel);

        $attributeOptionMock->expects($this->once())
            ->method('setStoreLabels')
            ->with([$firstStoreLabel, $secondStoreLabel]);

        $this->attributeOptionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($attributeOptionMock);

        $this->assertSame($attributeOptionMock, $this->model->toSwatchAttributeOption($filterSwatchItem));
    }

    /**
     * Retrieve store value mock object
     *
     * @param int $storeId
     * @param string $value
     * @return StoreValueInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getStoreValueMock($storeId, $value)
    {
        $storeValueMock = $this->createMock(StoreValueInterface::class);

        $storeValueMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $storeValueMock->expects($this->any())
            ->method('getValue')
            ->willReturn($value);

        return $storeValueMock;
    }
}
