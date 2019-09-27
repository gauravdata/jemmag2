<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter;

use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection as FulltextCollection;
use Magento\Framework\Exception\StateException;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\DataResolver
 */
class DataResolverTest extends TestCase
{
    /**
     * @var DataResolver
     */
    private $model;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->model = $objectManager->getObject(DataResolver::class, []);
    }

    /**
     * Test getFacetedData method
     *
     * @param string $code
     * @param bool $callByCode
     * @param array $facetedData
     * @dataProvider getFacetedDataDataProvider
     * @throws \Magento\Framework\Exception\StateException
     * @throws \ReflectionException
     */
    public function testGetFacetedData($code, $callByCode, $facetedData)
    {
        $productCollectionMock = $this->createMock(FulltextCollection::class);
        $productCollectionMock->expects($this->once())
            ->method('getFacetedData')
            ->with($code)
            ->willReturn($facetedData);

        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->once())
            ->method('getProductCollection')
            ->willReturn($productCollectionMock);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getLayer')
            ->willReturn($layerMock);

        if ($callByCode) {
            $filterMock->expects($this->never())
                ->method('getAttributeModel');

            $this->assertEquals($facetedData, $this->model->getFacetedData($filterMock, $code));
        } else {
            $attributeMock = $this->createMock(EavAttribute::class);
            $attributeMock->expects($this->once())
                ->method('getAttributeCode')
                ->willReturn($code);

            $filterMock->expects($this->once())
                ->method('getAttributeModel')
                ->willReturn($attributeMock);

            $this->assertEquals($facetedData, $this->model->getFacetedData($filterMock));
        }
    }

    /**
     * @return array
     */
    public function getFacetedDataDataProvider()
    {
        return [
            [
                'code' => 'attr_code',
                'callByCode' => true,
                'facetedData' => [
                    '*_10' => ['value' => '*_10', 'count' => 1],
                    '10_20' => ['value' => '10_20', 'count' => 2],
                    '20_*' => ['value' => '20_*', 'count' => 3]
                ]
            ],
            [
                'code' => 'attr_code',
                'callByCode' => false,
                'facetedData' => [
                    '121' => ['value' => '121', 'count' => 1],
                    '122' => ['value' => '122', 'count' => 2],
                    '123' => ['value' => '123', 'count' => 3]
                ]
            ],
        ];
    }

    /**
     * Test getFacetedData method if an exception occurs
     */
    public function testGetFacetedDataException()
    {
        $code = 'attr_code';

        $productCollectionMock = $this->createMock(FulltextCollection::class);
        $productCollectionMock->expects($this->once())
            ->method('getFacetedData')
            ->with($code)
            ->willThrowException(new StateException(__('Error!')));

        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->once())
            ->method('getProductCollection')
            ->willReturn($productCollectionMock);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getLayer')
            ->willReturn($layerMock);

        $attributeMock = $this->createMock(EavAttribute::class);
        $attributeMock->expects($this->once())
            ->method('getAttributeCode')
            ->willReturn($code);

        $filterMock->expects($this->once())
            ->method('getAttributeModel')
            ->willReturn($attributeMock);

        $this->assertEquals([], $this->model->getFacetedData($filterMock));
    }
}
