<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Model\Filter\CustomFilterChecker;
use Aheadworks\Layerednav\Model\Layer\FilterList;
use Aheadworks\Layerednav\Model\Layer\Filter as LayerFilter;
use Aheadworks\Layerednav\Model\Layer\FilterFactory as LayerFilterFactory;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\CategoryValidator as FilterCategoryValidator;
use Aheadworks\Layerednav\Model\Layer\FilterList\AttributeProviderInterface;
use Aheadworks\Layerednav\Model\Layer\FilterList\FilterProviderInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Category as CategoryModel;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Psr\Log\LoggerInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\FilterList
 */
class FilterListTest extends TestCase
{
    /**
     * @var FilterList
     */
    private $model;

    /**
     * @var LayerFilterFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerFilterFactoryMock;

    /**
     * @var FilterProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterProviderMock;

    /**
     * @var AttributeProviderInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeProviderMock;

    /**
     * @var CustomFilterChecker|\PHPUnit_Framework_MockObject_MockObject
     */
    private $customFilterCheckerMock;

    /**
     * @var FilterCategoryValidator|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterCategoryValidatorMock;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->layerFilterFactoryMock = $this->createMock(LayerFilterFactory::class);
        $this->filterProviderMock = $this->createMock(FilterProviderInterface::class);
        $this->attributeProviderMock = $this->createMock(AttributeProviderInterface::class);
        $this->customFilterCheckerMock = $this->createMock(CustomFilterChecker::class);
        $this->filterCategoryValidatorMock = $this->createMock(FilterCategoryValidator::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);

        $this->model = $objectManager->getObject(
            FilterList::class,
            [
                'layerFilterFactory' => $this->layerFilterFactoryMock,
                'filterProvider' => $this->filterProviderMock,
                'attributeProvider' => $this->attributeProviderMock,
                'customFilterChecker' => $this->customFilterCheckerMock,
                'filterCategoryValidator' => $this->filterCategoryValidatorMock,
                'logger' => $this->loggerMock,
            ]
        );
    }

    /**
     * Test getFilters method
     *
     * @param string $filterType
     * @param bool $isValidForCategory
     * @param bool $isCustom
     * @param bool $isAvailable
     * @param string $attributeCode
     * @param bool $emptyResult
     * @throws \Exception
     * @dataProvider getFiltersDataProvider
     */
    public function testGetFilters(
        $filterType,
        $isValidForCategory,
        $isCustom,
        $isAvailable,
        $attributeCode,
        $emptyResult
    ) {
        $categoryModelMock = $this->createMock(CategoryModel::class);
        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->once())
            ->method('getCurrentCategory')
            ->willReturn($categoryModelMock);

        $filterObjectMock = $this->createMock(FilterInterface::class);
        $filterObjects = [$filterObjectMock];
        $this->filterProviderMock->expects($this->once())
            ->method('getFilterDataObjects')
            ->willReturn($filterObjects);

        $attributeMock = $this->createMock(Attribute::class);
        $attributes = [$attributeCode => $attributeMock];
        $this->attributeProviderMock->expects($this->once())
            ->method('getAttributes')
            ->willReturn($attributes);

        $this->filterCategoryValidatorMock->expects($this->once())
            ->method('validate')
            ->with($filterObjectMock, $categoryModelMock)
            ->willReturn($isValidForCategory);

        if ($isValidForCategory) {
            $filterObjectMock->expects($this->atLeastOnce())
                ->method('getType')
                ->willReturn($filterType);

            $filterMock = $this->createMock(LayerFilter::class);

            $this->customFilterCheckerMock->expects($this->once())
                ->method('isCustom')
                ->with($filterType)
                ->willReturn($isCustom);

            if ($isCustom) {
                $this->customFilterCheckerMock->expects($this->once())
                    ->method('isAvailable')
                    ->with($filterType)
                    ->willReturn($isAvailable);

                if (!$emptyResult) {
                    $this->layerFilterFactoryMock->expects($this->once())
                        ->method('create')
                        ->with($filterObjectMock, $layerMock)
                        ->willReturn($filterMock);
                }
            } else {
                $filterObjectMock->expects($this->once())
                    ->method('getCode')
                    ->willReturn($attributeCode);

                $this->layerFilterFactoryMock->expects($this->once())
                    ->method('create')
                    ->with($filterObjectMock, $layerMock, $attributeMock)
                    ->willReturn($filterMock);
            }
        }

        $this->loggerMock->expects($this->never())
            ->method('critical');

        if ($emptyResult) {
            $this->assertEquals([], $this->model->getFilters($layerMock));
        } else {
            $this->assertEquals([$filterMock], $this->model->getFilters($layerMock));
        }
    }

    /**
     * @return array
     */
    public function getFiltersDataProvider()
    {
        return [
            [
                'filterType'            => FilterInterface::ATTRIBUTE_FILTER,
                'isValidForCategory'    => true,
                'isCustom'              => false,
                'isAvailable'           => true,
                'attributeCode'         => 'color',
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::ATTRIBUTE_FILTER,
                'isValidForCategory'    => false,
                'isCustom'              => false,
                'isAvailable'           => true,
                'attributeCode'         => 'color',
                'emptyResult'           => true
            ],
            [
                'filterType'            => FilterInterface::PRICE_FILTER,
                'isValidForCategory'    => true,
                'isCustom'              => false,
                'isAvailable'           => true,
                'attributeCode'         => 'price',
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::DECIMAL_FILTER,
                'isValidForCategory'    => true,
                'isCustom'              => false,
                'isAvailable'           => true,
                'attributeCode'         => 'cost',
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::CATEGORY_FILTER,
                'isValidForCategory'    => true,
                'isCustom'              => true,
                'isAvailable'           => true,
                'attributeCode'         => null,
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::NEW_FILTER,
                'isValidForCategory'    => true,
                'isCustom'              => true,
                'isAvailable'           => true,
                'attributeCode'         => null,
                'emptyResult'           => false
            ],
            [
                'filterType'            => FilterInterface::STOCK_FILTER,
                'isValidForCategory'    => true,
                'isCustom'              => true,
                'isAvailable'           => false,
                'attributeCode'         => null,
                'emptyResult'           => true
            ],
            [
                'filterType'            => FilterInterface::SALES_FILTER,
                'isValidForCategory'    => true,
                'isCustom'              => true,
                'isAvailable'           => false,
                'attributeCode'         => null,
                'emptyResult'           => true
            ],
        ];
    }

    /**
     * Test getFilters method if an error occurs
     *
     * @param bool $isCustom
     * @param string $errorMessage
     * @param array $expectedResult
     * @dataProvider getFiltersErrorDataProvider
     */
    public function testGetFiltersError($isCustom, $errorMessage, $expectedResult)
    {
        $attributeCode = 'attr_code';
        $filterType = 'filter_type';

        $categoryModelMock = $this->createMock(CategoryModel::class);
        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->once())
            ->method('getCurrentCategory')
            ->willReturn($categoryModelMock);

        $filterObjectMock = $this->createMock(FilterInterface::class);
        $filterObjects = [$filterObjectMock];
        $this->filterProviderMock->expects($this->once())
            ->method('getFilterDataObjects')
            ->willReturn($filterObjects);

        $attributeMock = $this->createMock(Attribute::class);
        $attributes = [$attributeCode => $attributeMock];
        $this->attributeProviderMock->expects($this->once())
            ->method('getAttributes')
            ->willReturn($attributes);

        $this->filterCategoryValidatorMock->expects($this->once())
            ->method('validate')
            ->with($filterObjectMock, $categoryModelMock)
            ->willReturn(true);

        $filterObjectMock->expects($this->atLeastOnce())
            ->method('getType')
            ->willReturn($filterType);

        $this->customFilterCheckerMock->expects($this->once())
            ->method('isCustom')
            ->with($filterType)
            ->willReturn($isCustom);

        if ($isCustom) {
            $this->customFilterCheckerMock->expects($this->once())
                ->method('isAvailable')
                ->with($filterType)
                ->willReturn(true);

            $this->layerFilterFactoryMock->expects($this->once())
                ->method('create')
                ->with($filterObjectMock, $layerMock)
                ->willThrowException(new \Exception($errorMessage));
        } else {
            $filterObjectMock->expects($this->once())
                ->method('getCode')
                ->willReturn($attributeCode);

            $this->layerFilterFactoryMock->expects($this->once())
                ->method('create')
                ->with($filterObjectMock, $layerMock, $attributeMock)
                ->willThrowException(new \Exception($errorMessage));
        }

        $this->loggerMock->expects($this->once())
            ->method('critical')
            ->with($errorMessage);

        $this->assertEquals($expectedResult, $this->model->getFilters($layerMock));
    }

    /**
     * @return array
     */
    public function getFiltersErrorDataProvider()
    {
        return [
            [
                'isCustom' => true,
                'errorMessage' => 'Custom filter exception',
                'expectedResult' => []
            ],
            [
                'isCustom' => false,
                'errorMessage' => 'Attribute filter exception',
                'expectedResult' => []
            ],
        ];
    }
}
