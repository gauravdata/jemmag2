<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model;

use Aheadworks\Layerednav\Model\FilterManagement;
use Aheadworks\Layerednav\Model\FilterManagement\AttributeProcessor;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterfaceFactory;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Source\Filter\Types as FilterTypesSource;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterfaceFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Eav\Api\Data\AttributeOptionLabelInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Test for \Aheadworks\Layerednav\Model\FilterManagement
 */
class FilterManagementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterManagement
     */
    private $model;

    /**
     * @var AttributeCollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeCollectionFactoryMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var FilterInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterFactoryMock;

    /**
     * @var FilterRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterRepositoryMock;

    /**
     * @var ProductAttributeRepositoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productAttributeRepositoryMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilderMock;

    /**
     * @var FilterTypesSource|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterTypesSourceMock;

    /**
     * @var StoreValueInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeValueFactoryMock;

    /**
     * @var AttributeProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeProcessorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->attributeCollectionFactoryMock = $this->getMockBuilder(AttributeCollectionFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeManagerMock = $this->getMockBuilder(StoreManagerInterface::class)
            ->getMockForAbstractClass();

        $this->filterFactoryMock = $this->getMockBuilder(FilterInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterRepositoryMock = $this->getMockBuilder(FilterRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->productAttributeRepositoryMock = $this->getMockBuilder(ProductAttributeRepositoryInterface::class)
            ->getMockForAbstractClass();

        $this->searchCriteriaBuilderMock = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->setMethods(['addFilter', 'create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->filterTypesSourceMock = $this->getMockBuilder(FilterTypesSource::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $this->storeValueFactoryMock = $this->getMockBuilder(StoreValueInterfaceFactory::class)
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeProcessorMock = $this->getMockBuilder(AttributeProcessor::class)
            ->setMethods(['getStorefrontTitles', 'getAttributeLabels', 'isLabelsDifferent'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = $objectManager->getObject(
            FilterManagement::class,
            [
                'attributeCollectionFactory' => $this->attributeCollectionFactoryMock,
                'storeManager' => $this->storeManagerMock,
                'filterFactory' => $this->filterFactoryMock,
                'filterRepository' => $this->filterRepositoryMock,
                'productAttributeRepository' => $this->productAttributeRepositoryMock,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilderMock,
                'filterTypesSource' => $this->filterTypesSourceMock,
                'storeValueFactory' => $this->storeValueFactoryMock,
                'attributeProcessor' => $this->attributeProcessorMock,
            ]
        );
    }

    /**
     * Test createFilter method
     *
     * @param array $filter
     * @param array $attribute
     * @param bool $result
     * @dataProvider getAttributeDataProvider
     */
    public function testCreateFilter($filter, $attribute, $result)
    {
        $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->getMockForAbstractClass();
        $attributeMock->expects($this->atLeastOnce())
            ->method('getIsFilterable')
            ->willReturn($attribute['is_filterable']);
        $attributeMock->expects($this->atLeastOnce())
            ->method('getIsFilterableInSearch')
            ->willReturn($attribute['is_filterable_in_search']);

        if ($attribute['is_filterable'] || $attribute['is_filterable_in_search']) {
            $filterMock = $this->getMockBuilder(FilterInterface::class)
                ->getMockForAbstractClass();
            $this->filterFactoryMock->expects($this->once())
                ->method('create')
                ->willReturn($filterMock);

            $attributeMock->expects($this->once())
                ->method('getDefaultFrontendLabel')
                ->willReturn($attribute['default_frontend_label']);
            $attributeMock->expects($this->exactly(2))
                ->method('getAttributeCode')
                ->willReturn($attribute['attribute_code']);
            $attributeMock->expects($this->once())
                ->method('getPosition')
                ->willReturn($attribute['position']);
            $attributeMock->expects($this->any())
                ->method('getBackendType')
                ->willReturn($attribute['backend_type']);

            $sortOrderValueMock = $this->getMockBuilder(StoreValueInterface::class)
                ->getMockForAbstractClass();
            $this->storeValueFactoryMock->expects($this->once())
                ->method('create')
                ->willReturn($sortOrderValueMock);
            $sortOrderValueMock->expects($this->once())
                ->method('setStoreId')
                ->with(Store::DEFAULT_STORE_ID)
                ->willReturnSelf();
            $sortOrderValueMock->expects($this->once())
                ->method('setValue')
                ->with(FilterInterface::SORT_ORDER_MANUAL)
                ->willReturnSelf();

            $titleValueMock = $this->getMockBuilder(StoreValueInterface::class)
                ->getMockForAbstractClass();
            $this->attributeProcessorMock->expects($this->once())
                ->method('getStorefrontTitles')
                ->with($attributeMock)
                ->willReturn([$titleValueMock]);

            $filterMock->expects($this->once())
                ->method('setDefaultTitle')
                ->with($attribute['default_frontend_label'])
                ->willReturnSelf();
            $filterMock->expects($this->once())
                ->method('setStorefrontTitles')
                ->with([$titleValueMock])
                ->willReturnSelf();
            $filterMock->expects($this->once())
                ->method('setCode')
                ->with($attribute['attribute_code'])
                ->willReturnSelf();
            $filterMock->expects($this->once())
                ->method('setType')
                ->with($filter['type'])
                ->willReturnSelf();
            $filterMock->expects($this->once())
                ->method('setIsFilterable')
                ->with($attribute['is_filterable'])
                ->willReturnSelf();
            $filterMock->expects($this->once())
                ->method('setIsFilterableInSearch')
                ->with($attribute['is_filterable_in_search'])
                ->willReturnSelf();
            $filterMock->expects($this->once())
                ->method('setPosition')
                ->with($attribute['position'])
                ->willReturnSelf();
            $filterMock->expects($this->once())
                ->method('setSortOrders')
                ->with([$sortOrderValueMock])
                ->willReturnSelf();
            $filterMock->expects($this->once())
                ->method('setCategoryMode')
                ->with(FilterInterface::CATEGORY_MODE_ALL)
                ->willReturnSelf();

            $this->filterRepositoryMock->expects($this->once())
                ->method('save')
                ->with($filterMock)
                ->willReturn($filterMock);
        }

        $this->assertEquals($result, $this->model->createFilter($attributeMock));
    }

    /**
     * @return array
     */
    public function getAttributeDataProvider()
    {
        return [
            [
                'filter' => ['type' => FilterInterface::ATTRIBUTE_FILTER],
                'attribute' => [
                    'attribute_code' => 'color',
                    'default_frontend_label' => 'Color',
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 1,
                    'position' => 10,
                    'backend_type' => 'int',
                ],
                true
            ],
            [
                'filter' => ['type' => FilterInterface::ATTRIBUTE_FILTER],
                'attribute' => [
                    'attribute_code' => 'climate',
                    'default_frontend_label' => 'Climate',
                    'is_filterable' => 0,
                    'is_filterable_in_search' => 0,
                    'position' => 10,
                    'backend_type' => 'varchar',
                ],
                false
            ],

            [
                'filter' => ['type' => FilterInterface::PRICE_FILTER],
                'attribute' => [
                    'attribute_code' => 'price',
                    'default_frontend_label' => 'Price',
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 1,
                    'position' => 10,
                    'backend_type' => 'decimal',
                ],
                true
            ],
            [
                'filter' => ['id' => 1, 'code' => 'special_price', 'type' => FilterInterface::DECIMAL_FILTER],
                'attribute' => [
                    'attribute_code' => 'special_price',
                    'default_frontend_label' => 'Special Price',
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 1,
                    'position' => 10,
                    'backend_type' => 'decimal',
                ],
                true
            ],
        ];
    }

    /**
     * Test synchronizeFilterById method
     *
     * @param array $filter
     * @param array $attribute
     * @dataProvider getFilterDataProvider
     */
    public function testSynchronizeFilterById($filter, $attribute)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getType')
            ->willReturn($filter['type']);

        $this->filterRepositoryMock->expects($this->once())
            ->method('get')
            ->with($filter['id'])
            ->willReturn($filterMock);

        if (count($attribute) > 0) {
            $filterMock->expects($this->atLeastOnce())
                ->method('getCode')
                ->willReturn($filter['code']);

            $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
                ->getMockForAbstractClass();
            $attributeMock->expects($this->atLeastOnce())
                ->method('getIsFilterable')
                ->willReturn($attribute['is_filterable']);
            $attributeMock->expects($this->atLeastOnce())
                ->method('getIsFilterableInSearch')
                ->willReturn($attribute['is_filterable_in_search']);
            $this->productAttributeRepositoryMock->expects($this->once())
                ->method('get')
                ->with($filter['code'])
                ->willReturn($attributeMock);

            $attributeMock->expects($this->atLeastOnce())
                ->method('getAttributeCode')
                ->willReturn($attribute['attribute_code']);

            if ($attribute['is_filterable'] || $attribute['is_filterable_in_search']) {
                $attributeMock->expects($this->once())
                    ->method('getDefaultFrontendLabel')
                    ->willReturn($attribute['default_frontend_label']);
                $attributeMock->expects($this->once())
                    ->method('getPosition')
                    ->willReturn($attribute['position']);
                $attributeMock->expects($this->any())
                    ->method('getBackendType')
                    ->willReturn($attribute['backend_type']);

                $titleValueMock = $this->getMockBuilder(StoreValueInterface::class)
                    ->getMockForAbstractClass();
                $this->attributeProcessorMock->expects($this->once())
                    ->method('getStorefrontTitles')
                    ->with($attributeMock)
                    ->willReturn([$titleValueMock]);

                $filterMock->expects($this->once())
                    ->method('setDefaultTitle')
                    ->with($attribute['default_frontend_label'])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setStorefrontTitles')
                    ->with([$titleValueMock])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setType')
                    ->with($filter['type'])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setIsFilterable')
                    ->with($attribute['is_filterable'])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setIsFilterableInSearch')
                    ->with($attribute['is_filterable_in_search'])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setPosition')
                    ->with($attribute['position'])
                    ->willReturnSelf();

                $this->filterRepositoryMock->expects($this->once())
                    ->method('save')
                    ->with($filterMock)
                    ->willReturn($filterMock);
            } else {
                $this->filterRepositoryMock->expects($this->once())
                    ->method('delete')
                    ->with($filterMock)
                    ->willReturn(true);
            }
        }
        self::assertTrue($this->model->synchronizeFilterById($filter['id']));
    }

    /**
     * Test synchronizeFilterById method id no filter found
     */
    public function testSynchronizeFilterByIdNoFilter()
    {
        $filterId = 1;

        $this->filterRepositoryMock->expects($this->once())
            ->method('get')
            ->with($filterId)
            ->willThrowException(new NoSuchEntityException(__("No such entity with id = ?", $filterId)));

        self::assertFalse($this->model->synchronizeFilterById($filterId));
    }

    /**
     * Test synchronizeFilterById method id no attribute found
     *
     * @param array $filter
     * @param array $attribute
     * @dataProvider getFilterDataProvider
     */
    public function testSynchronizeFilterByIdNoAttribute($filter, $attribute)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getType')
            ->willReturn($filter['type']);

        $this->filterRepositoryMock->expects($this->once())
            ->method('get')
            ->with($filter['id'])
            ->willReturn($filterMock);

        if (count($attribute) > 0) {
            $filterMock->expects($this->once())
                ->method('getCode')
                ->willReturn($filter['code']);

            $this->productAttributeRepositoryMock->expects($this->once())
                ->method('get')
                ->with($filter['code'])
                ->willThrowException(new NoSuchEntityException(__("No such entity with code = ?", $filter['code'])));

            self::assertFalse($this->model->synchronizeFilterById($filter['id']));
        } else {
            self::assertTrue($this->model->synchronizeFilterById($filter['id']));
        }
    }

    /**
     * Test synchronizeFilter method
     *
     * @param array $filter
     * @param array $attribute
     * @dataProvider getFilterDataProvider
     */
    public function testSynchronizeFilter($filter, $attribute)
    {
        if (count($attribute) > 0) {
            $filterMock = $this->getMockBuilder(FilterInterface::class)
                ->getMockForAbstractClass();
            $filterMock->expects($this->atLeastOnce())
                ->method('getCode')
                ->willReturn($filter['code']);

            $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
                ->getMockForAbstractClass();
            $attributeMock->expects($this->atLeastOnce())
                ->method('getAttributeCode')
                ->willReturn($attribute['attribute_code']);
            $attributeMock->expects($this->atLeastOnce())
                ->method('getIsFilterable')
                ->willReturn($attribute['is_filterable']);
            $attributeMock->expects($this->atLeastOnce())
                ->method('getIsFilterableInSearch')
                ->willReturn($attribute['is_filterable_in_search']);

            if ($attribute['is_filterable'] || $attribute['is_filterable_in_search']) {
                $attributeMock->expects($this->once())
                    ->method('getDefaultFrontendLabel')
                    ->willReturn($attribute['default_frontend_label']);
                $attributeMock->expects($this->once())
                    ->method('getPosition')
                    ->willReturn($attribute['position']);
                $attributeMock->expects($this->any())
                    ->method('getBackendType')
                    ->willReturn($attribute['backend_type']);

                $titleValueMock = $this->getMockBuilder(StoreValueInterface::class)
                    ->getMockForAbstractClass();
                $this->attributeProcessorMock->expects($this->once())
                    ->method('getStorefrontTitles')
                    ->with($attributeMock)
                    ->willReturn([$titleValueMock]);

                $filterMock->expects($this->once())
                    ->method('setDefaultTitle')
                    ->with($attribute['default_frontend_label'])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setStorefrontTitles')
                    ->with([$titleValueMock])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setType')
                    ->with($filter['type'])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setIsFilterable')
                    ->with($attribute['is_filterable'])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setIsFilterableInSearch')
                    ->with($attribute['is_filterable_in_search'])
                    ->willReturnSelf();
                $filterMock->expects($this->once())
                    ->method('setPosition')
                    ->with($attribute['position'])
                    ->willReturnSelf();

                $this->filterRepositoryMock->expects($this->once())
                    ->method('save')
                    ->with($filterMock)
                    ->willReturn($filterMock);
            } else {
                $this->filterRepositoryMock->expects($this->once())
                    ->method('delete')
                    ->with($filterMock)
                    ->willReturn(true);
            }

            self::assertTrue($this->model->synchronizeFilter($filterMock, $attributeMock));
        }
    }

    /**
     * @return array
     */
    public function getFilterDataProvider()
    {
        return [
            [
                'filter' => ['id' => 1, 'type' => FilterInterface::CATEGORY_FILTER],
                'attribute' => [],
            ],
            [
                'filter' => ['id' => 1, 'type' => FilterInterface::NEW_FILTER],
                'attribute' => [],
            ],
            [
                'filter' => ['id' => 1, 'type' => FilterInterface::SALES_FILTER],
                'attribute' => [],
            ],
            [
                'filter' => ['id' => 1, 'type' => FilterInterface::STOCK_FILTER],
                'attribute' => [],
            ],
            [
                'filter' => ['id' => 1, 'code' => 'color', 'type' => FilterInterface::ATTRIBUTE_FILTER],
                'attribute' => [
                    'attribute_code' => 'color',
                    'default_frontend_label' => 'Color',
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 1,
                    'position' => 10,
                    'backend_type' => 'int',
                ],
            ],
            [
                'filter' => ['id' => 1, 'code' => 'climate', 'type' => FilterInterface::ATTRIBUTE_FILTER],
                'attribute' => [
                    'attribute_code' => 'climate',
                    'default_frontend_label' => 'Climate',
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 0,
                    'position' => 10,
                    'backend_type' => 'varchar',
                ],
            ],
            [
                'filter' => ['id' => 1, 'code' => 'activity', 'type' => FilterInterface::ATTRIBUTE_FILTER],
                'attribute' => [
                    'attribute_code' => 'activity',
                    'default_frontend_label' => 'Activity',
                    'is_filterable' => 0,
                    'is_filterable_in_search' => 0,
                    'position' => 10,
                    'backend_type' => 'varchar',
                ],
            ],
            [
                'filter' => ['id' => 1, 'code' => 'price', 'type' => FilterInterface::PRICE_FILTER],
                'attribute' => [
                    'attribute_code' => 'price',
                    'default_frontend_label' => 'Price',
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 1,
                    'position' => 10,
                    'backend_type' => 'decimal',
                ],
            ],
            [
                'filter' => ['id' => 1, 'code' => 'special_price', 'type' => FilterInterface::DECIMAL_FILTER],
                'attribute' => [
                    'attribute_code' => 'special_price',
                    'default_frontend_label' => 'Special Price',
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 1,
                    'position' => 10,
                    'backend_type' => 'decimal',
                ],
            ],
        ];
    }

    /**
     * Test synchronizeAttribute method (the filter with an attribute)
     *
     * @param array $filter
     * @param array $attribute
     * @dataProvider getAttributeFilterDataProvider
     */
    public function testSynchronizeAttribute($filter, $attribute)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();

        $this->filterRepositoryMock->expects($this->once())
            ->method('get')
            ->with($filter['id'])
            ->willReturn($filterMock);

        $attributeMock = $this->getAttributeMock();

        $this->productAttributeRepositoryMock->expects($this->once())
            ->method('get')
            ->with($filter['code'])
            ->willReturn($attributeMock);

        $filterMock->expects($this->once())
            ->method('getType')
            ->willReturn($filter['type']);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($filter['code']);
        $filterMock->expects($this->once())
            ->method('getDefaultTitle')
            ->willReturn($filter['title']);
        $filterMock->expects($this->once())
            ->method('getIsFilterable')
            ->willReturn($filter['is_filterable']);
        $filterMock->expects($this->once())
            ->method('getIsFilterableInSearch')
            ->willReturn($filter['is_filterable_in_search']);
        $filterMock->expects($this->once())
            ->method('getPosition')
            ->willReturn($filter['position']);

        $attributeLabelMock = $this->getMockBuilder(AttributeOptionLabelInterface::class)
            ->getMockForAbstractClass();
        $this->attributeProcessorMock->expects($this->once())
            ->method('isLabelsDifferent')
            ->with($attributeMock, $filterMock)
            ->willReturn(true);
        $this->attributeProcessorMock->expects($this->once())
            ->method('getAttributeLabels')
            ->with($filterMock)
            ->willReturn([$attributeLabelMock]);

        $attributeMock->expects($this->any())
            ->method('getDefaultFrontendLabel')
            ->willReturn($attribute['default_frontend_label']);
        $attributeMock->expects($this->any())
            ->method('getIsFilterable')
            ->willReturn($attribute['is_filterable']);
        $attributeMock->expects($this->any())
            ->method('getIsFilterableInSearch')
            ->willReturn($attribute['is_filterable_in_search']);
        $attributeMock->expects($this->any())
            ->method('getPosition')
            ->willReturn($attribute['position']);
        $attributeMock->expects($this->once())
            ->method('setDefaultFrontendLabel')
            ->with($filter['title'])
            ->willReturnSelf();
        $attributeMock->expects($this->once())
            ->method('setFrontendLabels')
            ->with([$attributeLabelMock])
            ->willReturnSelf();
        $attributeMock->expects($this->once())
            ->method('setIsFilterable')
            ->with($filter['is_filterable'])
            ->willReturnSelf();
        $attributeMock->expects($this->once())
            ->method('setIsFilterableInSearch')
            ->with($filter['is_filterable_in_search'])
            ->willReturnSelf();
        $attributeMock->expects($this->once())
            ->method('setPosition')
            ->with($filter['position'])
            ->willReturnSelf();

        $attributeMock->expects($this->once())
            ->method('setData')
            ->with(FilterManagement::NO_NEED_TO_SYNCHRONIZE_FILTER_FLAG, true)
            ->willReturnSelf();

        $this->productAttributeRepositoryMock->expects($this->once())
            ->method('save')
            ->with($attributeMock)
            ->willReturn($attributeMock);

        $this->assertTrue($this->model->synchronizeAttribute($filter['id'], false));
    }

    /**
     * Test synchronizeAttribute method if no filter can be loaded
     *
     */
    public function testSynchronizeAttributeNoFilterError()
    {
        $filterId = 1;
        $filterCode = 'color';
        $filterType = FilterInterface::ATTRIBUTE_FILTER;
        $exception = new NoSuchEntityException();

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterMock->expects($this->once())
            ->method('getType')
            ->willReturn($filterType);
        $filterMock->expects($this->once())
            ->method('getCode')
            ->willReturn($filterCode);

        $this->filterRepositoryMock->expects($this->once())
            ->method('get')
            ->with($filterId)
            ->willReturn($filterMock);

        $this->productAttributeRepositoryMock->expects($this->once())
            ->method('get')
            ->with($filterCode)
            ->willThrowException($exception);

        $this->assertFalse($this->model->synchronizeAttribute($filterId, false));
    }

    /**
     * Test synchronizeAttribute method if no linked attribute can be loaded
     *
     */
    public function testSynchronizeAttributeNoAttributeError()
    {
        $filterId = 1;
        $exception = new NoSuchEntityException();

        $this->filterRepositoryMock->expects($this->once())
            ->method('get')
            ->with($filterId)
            ->willThrowException($exception);

        $this->assertFalse($this->model->synchronizeAttribute($filterId, false));
    }

    /**
     * Test synchronizeAttribute method (the filter without an attribute)
     *
     * @param array $filter
     * @dataProvider getCustomFilterDataProvider
     */
    public function testSynchronizeAttributeCustomFilter($filter)
    {
        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();

        $this->filterRepositoryMock->expects($this->exactly(2))
            ->method('get')
            ->with($filter['id'])
            ->willReturn($filterMock);

        $filterMock->expects($this->exactly(2))
            ->method('getType')
            ->willReturn($filter['type']);

        $this->assertFalse($this->model->synchronizeAttribute($filter['id'], false));
        $this->assertTrue($this->model->synchronizeAttribute($filter['id'], true));
    }

    /**
     * @return array
     */
    public function getAttributeFilterDataProvider()
    {
        $attributeFiltersData = [];
        foreach (FilterInterface::ATTRIBUTE_FILTER_TYPES as $attributeFilterType) {
            $attributeFiltersData[] = [
                'filter' => [
                    'id' => 1,
                    'title' => 'Color_new',
                    'code' => 'color',
                    'type' => $attributeFilterType,
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 0,
                    'position' => 99,
                ],
                'attribute' => [
                    'id' => 2,
                    'default_frontend_label' => 'Color',
                    'is_filterable' => 1,
                    'is_filterable_in_search' => 1,
                    'position' => 10,
                ],
            ];
        }
        return $attributeFiltersData;
    }

    /**
     * @return array
     */
    public function getCustomFilterDataProvider()
    {
        $customFiltersData = [];
        foreach (FilterInterface::CUSTOM_FILTER_TYPES as $customFilterType) {
            $customFiltersData[] = [
                'filter' => [
                    'id' => 1,
                    'type' => $customFilterType,
                ],
            ];
        }
        return $customFiltersData;
    }

    /**
     * Test synchronizeCustomFilters method
     */
    public function testSynchronizeCustomFilters()
    {
        $savedFilterType = FilterInterface::CATEGORY_FILTER;
        $filterTypesToSave = [
            'aw_sales' => FilterInterface::SALES_FILTER,
            'aw_new' => FilterInterface::NEW_FILTER,
            'aw_stock' => FilterInterface::STOCK_FILTER,
        ];

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with(FilterInterface::TYPE, FilterInterface::CUSTOM_FILTER_TYPES, 'in')
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $savedFilterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $savedFilterMock->expects($this->once())
            ->method('getType')
            ->willReturn($savedFilterType);
        $filterSearchResultsMock = $this->getMockBuilder(FilterSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $filterSearchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$savedFilterMock]);
        $this->filterRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($filterSearchResultsMock);

        $sortOrderValueMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $this->storeValueFactoryMock->expects($this->exactly(3))
            ->method('create')
            ->willReturn($sortOrderValueMock);
        $sortOrderValueMock->expects($this->exactly(3))
            ->method('setStoreId')
            ->with(Store::DEFAULT_STORE_ID)
            ->willReturnSelf();
        $sortOrderValueMock->expects($this->exactly(3))
            ->method('setValue')
            ->with(FilterInterface::SORT_ORDER_MANUAL)
            ->willReturnSelf();

        $newFilterMock = $this->getMockBuilder(FilterInterface::class)
        ->getMockForAbstractClass();
        $newFilterMock->expects($this->exactly(3))
            ->method('setDefaultTitle')
            ->willReturnSelf();
        $newFilterMock->expects($this->exactly(3))
            ->method('setCode')
            ->withConsecutive(
                ['aw_sales'],
                ['aw_new'],
                ['aw_stock']
            )
            ->willReturnSelf();
        $newFilterMock->expects($this->exactly(3))
            ->method('setType')
            ->withConsecutive(
                [$filterTypesToSave['aw_sales']],
                [$filterTypesToSave['aw_new']],
                [$filterTypesToSave['aw_stock']]
            )
            ->willReturnSelf();
        $newFilterMock->expects($this->exactly(3))
            ->method('setIsFilterable')
            ->willReturnSelf();
        $newFilterMock->expects($this->exactly(3))
            ->method('setIsFilterableInSearch')
            ->willReturnSelf();
        $newFilterMock->expects($this->exactly(3))
            ->method('setPosition')
            ->willReturnSelf();
        $newFilterMock->expects($this->exactly(3))
            ->method('setSortOrders')
            ->with([$sortOrderValueMock])
            ->willReturnSelf();
        $newFilterMock->expects($this->exactly(3))
            ->method('setCategoryMode')
            ->with(FilterInterface::CATEGORY_MODE_ALL)
            ->willReturnSelf();
        $this->filterFactoryMock->expects($this->exactly(3))
            ->method('create')
            ->willReturn($newFilterMock);

        $this->filterRepositoryMock->expects($this->exactly(3))
            ->method('save')
            ->with($newFilterMock)
            ->willReturn($newFilterMock);

        $this->model->synchronizeCustomFilters();
    }

    /**
     * Test synchronizeAttributeFilters method if the filter is already exist
     */
    public function testSynchronizeAttributeFiltersUpdateFilter()
    {
        $storeId = 1;
        $attributeId = 25;
        $attributeCode = 'color';
        $attributeLabel = 'Color';
        $attributeBackendType = 'int';
        $filterId = 2;
        $filterType = FilterInterface::ATTRIBUTE_FILTER;

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->exactly(2))
            ->method('getStore')
            ->willReturn($storeMock);

        $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->getMockForAbstractClass();
        $attributeCollection = $this->getMockBuilder(AttributeCollection::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeCollection->expects($this->exactly(2))
            ->method('setItemObjectClass')
            ->willReturnSelf();
        $attributeCollection->expects($this->exactly(2))
            ->method('addStoreLabel')
            ->with($storeId)
            ->willReturnSelf();
        $attributeCollection->expects($this->once())
            ->method('addIsFilterableFilter')
            ->willReturnSelf();
        $attributeCollection->expects($this->once())
            ->method('addIsFilterableInSearchFilter')
            ->willReturnSelf();
        $attributeCollection->expects($this->once())
            ->method('addVisibleFilter')
            ->willReturnSelf();
        $attributeCollection->expects($this->exactly(2))
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$attributeMock]));
        $this->attributeCollectionFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($attributeCollection);

        $attributeMock->expects($this->exactly(2))
            ->method('getAttributeId')
            ->willReturn($attributeId);
        $attributeMock->expects($this->exactly(4))
            ->method('getAttributeCode')
            ->willReturn($attributeCode);

        $filterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $this->filterRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($attributeCode, $filterType)
            ->willReturn($filterMock);

        $attributeMock->expects($this->exactly(2))
            ->method('getBackendType')
            ->willReturn($attributeBackendType);

        $attributeMock->expects($this->once())
            ->method('getDefaultFrontendLabel')
            ->willReturn($attributeLabel);

        $titleValueMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $this->attributeProcessorMock->expects($this->once())
            ->method('getStorefrontTitles')
            ->with($attributeMock)
            ->willReturn([$titleValueMock]);

        $filterMock->expects($this->once())
            ->method('getId')
            ->willReturn($filterId);
        $filterMock->expects($this->once())
            ->method('setDefaultTitle')
            ->with($attributeLabel)
            ->willReturnSelf();
        $filterMock->expects($this->once())
            ->method('setStorefrontTitles')
            ->with([$titleValueMock])
            ->willReturnSelf();
        $filterMock->expects($this->once())
            ->method('setCode')
            ->willReturnSelf();
        $filterMock->expects($this->once())
            ->method('setType')
            ->willReturnSelf();
        $filterMock->expects($this->once())
            ->method('setIsFilterable')
            ->willReturnSelf();
        $filterMock->expects($this->once())
            ->method('setIsFilterableInSearch')
            ->willReturnSelf();
        $filterMock->expects($this->once())
            ->method('setPosition')
            ->willReturnSelf();

        $this->filterRepositoryMock->expects($this->once())
            ->method('save')
            ->with($filterMock)
            ->willReturn($filterMock);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive(
                [FilterInterface::ID, [$filterId], 'nin'],
                [FilterInterface::TYPE, FilterInterface::ATTRIBUTE_FILTER_TYPES, 'in']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $obsoletedFilter = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterSearchResults = $this->getMockBuilder(FilterSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $filterSearchResults->expects($this->once())
            ->method('getItems')
            ->willReturn([$obsoletedFilter]);

        $this->filterRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($filterSearchResults);
        $this->filterRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($obsoletedFilter)
            ->willReturn(true);

        $this->model->synchronizeAttributeFilters();
    }

    /**
     * Test synchronizeAttributeFilters method if new filter created
     */
    public function testSynchronizeAttributeFiltersNewFilter()
    {
        $storeId = 1;
        $attributeId = 25;
        $attributeCode = 'color';
        $attributeLabel = 'Color';
        $attributeBackendType = 'int';
        $filterId = 2;
        $filterType = FilterInterface::ATTRIBUTE_FILTER;

        $storeMock = $this->getMockBuilder(StoreInterface::class)
            ->getMockForAbstractClass();
        $storeMock->expects($this->exactly(2))
            ->method('getId')
            ->willReturn($storeId);
        $this->storeManagerMock->expects($this->exactly(2))
            ->method('getStore')
            ->willReturn($storeMock);

        $attributeMock = $this->getMockBuilder(ProductAttributeInterface::class)
            ->getMockForAbstractClass();
        $attributeCollection = $this->getMockBuilder(AttributeCollection::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $attributeCollection->expects($this->exactly(2))
            ->method('setItemObjectClass')
            ->willReturnSelf();
        $attributeCollection->expects($this->exactly(2))
            ->method('addStoreLabel')
            ->with($storeId)
            ->willReturnSelf();
        $attributeCollection->expects($this->once())
            ->method('addIsFilterableFilter')
            ->willReturnSelf();
        $attributeCollection->expects($this->once())
            ->method('addIsFilterableInSearchFilter')
            ->willReturnSelf();
        $attributeCollection->expects($this->once())
            ->method('addVisibleFilter')
            ->willReturnSelf();
        $attributeCollection->expects($this->exactly(2))
            ->method('getIterator')
            ->willReturn(new \ArrayIterator([$attributeMock]));
        $this->attributeCollectionFactoryMock->expects($this->exactly(2))
            ->method('create')
            ->willReturn($attributeCollection);

        $attributeMock->expects($this->exactly(2))
            ->method('getAttributeId')
            ->willReturn($attributeId);
        $attributeMock->expects($this->exactly(4))
            ->method('getAttributeCode')
            ->willReturn($attributeCode);

        $attributeMock->expects($this->exactly(2))
            ->method('getBackendType')
            ->willReturn($attributeBackendType);

        $this->filterRepositoryMock->expects($this->once())
            ->method('getByCode')
            ->with($attributeCode, $filterType)
            ->willThrowException(new NoSuchEntityException(__('No such entity with code = ?', $attributeCode)));

        $sortOrderValueMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $this->storeValueFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($sortOrderValueMock);
        $sortOrderValueMock->expects($this->once())
            ->method('setStoreId')
            ->with(Store::DEFAULT_STORE_ID)
            ->willReturnSelf();
        $sortOrderValueMock->expects($this->once())
            ->method('setValue')
            ->with(FilterInterface::SORT_ORDER_MANUAL)
            ->willReturnSelf();

        $newFilterMock = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $this->filterFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($newFilterMock);
        $newFilterMock->expects($this->once())
            ->method('setSortOrders')
            ->with([$sortOrderValueMock])
            ->willReturnSelf();
        $newFilterMock->expects($this->once())
            ->method('setCategoryMode')
            ->with(FilterInterface::CATEGORY_MODE_ALL)
            ->willReturnSelf();

        $attributeMock->expects($this->once())
            ->method('getDefaultFrontendLabel')
            ->willReturn($attributeLabel);

        $titleValueMock = $this->getMockBuilder(StoreValueInterface::class)
            ->getMockForAbstractClass();
        $this->attributeProcessorMock->expects($this->once())
            ->method('getStorefrontTitles')
            ->with($attributeMock)
            ->willReturn([$titleValueMock]);

        $newFilterMock->expects($this->once())
            ->method('getId')
            ->willReturn($filterId);
        $newFilterMock->expects($this->once())
            ->method('setDefaultTitle')
            ->with($attributeLabel)
            ->willReturnSelf();
        $newFilterMock->expects($this->once())
            ->method('setStorefrontTitles')
            ->with([$titleValueMock])
            ->willReturnSelf();
        $newFilterMock->expects($this->once())
            ->method('setCode')
            ->willReturnSelf();
        $newFilterMock->expects($this->once())
            ->method('setType')
            ->willReturnSelf();
        $newFilterMock->expects($this->once())
            ->method('setIsFilterable')
            ->willReturnSelf();
        $newFilterMock->expects($this->once())
            ->method('setIsFilterableInSearch')
            ->willReturnSelf();
        $newFilterMock->expects($this->once())
            ->method('setPosition')
            ->willReturnSelf();

        $this->filterRepositoryMock->expects($this->once())
            ->method('save')
            ->with($newFilterMock)
            ->willReturn($newFilterMock);

        $searchCriteriaMock = $this->getMockBuilder(SearchCriteria::class)
            ->setMethods([])
            ->disableOriginalConstructor()
            ->getMock();
        $this->searchCriteriaBuilderMock->expects($this->exactly(2))
            ->method('addFilter')
            ->withConsecutive(
                [FilterInterface::ID, [$filterId], 'nin'],
                [FilterInterface::TYPE, FilterInterface::ATTRIBUTE_FILTER_TYPES, 'in']
            )
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteriaMock);

        $obsoletedFilter = $this->getMockBuilder(FilterInterface::class)
            ->getMockForAbstractClass();
        $filterSearchResults = $this->getMockBuilder(FilterSearchResultsInterface::class)
            ->getMockForAbstractClass();
        $filterSearchResults->expects($this->once())
            ->method('getItems')
            ->willReturn([$obsoletedFilter]);

        $this->filterRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($filterSearchResults);
        $this->filterRepositoryMock->expects($this->once())
            ->method('delete')
            ->with($obsoletedFilter)
            ->willReturn(true);

        $this->model->synchronizeAttributeFilters();
    }

    /**
     * Get attribute mock
     *
     * @return AbstractAttribute|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getAttributeMock()
    {
        $attributeMock = $this->getMockForAbstractClass(
            ProductAttributeInterface::class,
            [],
            '',
            false,
            false,
            false,
            [
                'getDefaultFrontendLabel',
                'getIsFilterable',
                'getIsFilterableInSearch',
                'getPosition',
                'setDefaultFrontendLabel',
                'setFrontendLabels',
                'setIsFilterable',
                'setIsFilterableInSearch',
                'setPosition',
                'setData',
            ]
        );

        return $attributeMock;
    }
}
