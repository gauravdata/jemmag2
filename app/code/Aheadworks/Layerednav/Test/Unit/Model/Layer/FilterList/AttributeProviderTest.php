<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\FilterList;

use Aheadworks\Layerednav\Model\Layer\FilterList\AttributeProvider;
use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\FilterList\AttributeProvider
 */
class AttributeProviderTest extends TestCase
{
    /**
     * @var AttributeProvider
     */
    private $model;

    /**
     * @var FilterableAttributeListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterableAttributesMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filterableAttributesMock = $this->createMock(FilterableAttributeListInterface::class);

        $this->model = $objectManager->getObject(
            AttributeProvider::class,
            [
                'filterableAttributes' => $this->filterableAttributesMock,
            ]
        );
    }

    /**
     * Test getAttributes method
     *
     * @param bool $isArray
     * @dataProvider getAttributesDataProvider
     */
    public function testGetAttributes($isArray)
    {
        $attributeOneMock = $this->getAttributeCodeMock('attr-code-1');
        $attributeTwoMock = $this->getAttributeCodeMock('attr-code-2');
        $attributes = [$attributeOneMock, $attributeTwoMock];
        $expectedResult = [
            'attr-code-1' => $attributeOneMock,
            'attr-code-2' => $attributeTwoMock
        ];

        if ($isArray) {
            $this->filterableAttributesMock->expects($this->once())
                ->method('getList')
                ->willReturn($attributes);
        } else {
            $attributeCollectionMock = $this->createMock(AttributeCollection::class);
            $attributeCollectionMock->expects($this->once())
                ->method('getItems')
                ->willReturn($attributes);

            $this->filterableAttributesMock->expects($this->once())
                ->method('getList')
                ->willReturn($attributeCollectionMock);
        }

        $this->assertEquals($expectedResult, $this->model->getAttributes());
    }

    /**
     * Test getAttributes method if no attributes found
     *
     * @param bool $isArray
     * @dataProvider getAttributesDataProvider
     */
    public function testGetAttributesNoAttributes($isArray)
    {
        $attributes = [];
        $expectedResult = [];

        if ($isArray) {
            $this->filterableAttributesMock->expects($this->once())
                ->method('getList')
                ->willReturn($attributes);
        } else {
            $attributeCollectionMock = $this->createMock(AttributeCollection::class);
            $attributeCollectionMock->expects($this->once())
                ->method('getItems')
                ->willReturn($attributes);

            $this->filterableAttributesMock->expects($this->once())
                ->method('getList')
                ->willReturn($attributeCollectionMock);
        }

        $this->assertEquals($expectedResult, $this->model->getAttributes());
    }

    /**
     * @return array
     */
    public function getAttributesDataProvider()
    {
        return [
            ['isArray' => true],
            ['isArray' => false],
        ];
    }

    /**
     * Get attribute code
     *
     * @param string $code
     * @return Attribute|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getAttributeCodeMock($code)
    {
        $attributeMock = $this->createMock(Attribute::class);
        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($code);

        return $attributeMock;
    }
}
