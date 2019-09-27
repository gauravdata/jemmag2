<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\Filter as LayerFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Factory\DataProviderInterface as FilterDataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderInterface as ItemProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\ProviderFactory as ItemProviderFactory;
use Aheadworks\Layerednav\Model\Layer\FilterFactory;
use Aheadworks\Layerednav\Model\Layer\Filter\Factory\DataProviderPool as FilterDataProviderPool;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\FilterFactory
 */
class FilterFactoryTest extends TestCase
{
    /**
     * @var FilterFactory
     */
    private $model;

    /**
     * @var ObjectManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManagerMock;

    /**
     * @var ItemProviderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $itemProviderFactoryMock;

    /**
     * @var FilterDataProviderPool|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterDataProviderPoolMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->objectManagerMock = $this->createMock(ObjectManagerInterface::class);
        $this->itemProviderFactoryMock = $this->createMock(ItemProviderFactory::class);
        $this->filterDataProviderPoolMock = $this->createMock(FilterDataProviderPool::class);

        $this->model = $objectManager->getObject(
            FilterFactory::class,
            [
                'objectManager' => $this->objectManagerMock,
                'itemProviderFactory' => $this->itemProviderFactoryMock,
                'filterDataProviderPool' => $this->filterDataProviderPoolMock
            ]
        );
    }

    /**
     * Test create method
     *
     * @param Attribute|\PHPUnit_Framework_MockObject_MockObject|null $attribute
     * @dataProvider createDataProvider
     * @throws \ReflectionException
     */
    public function testCreate($attribute)
    {
        $type = 'filter_type';
        $sortOrder = 'asc';
        $filterData = ['<filter_data>'];

        $layerMock = $this->createMock(Layer::class);
        $filterMock = $this->getFilterMock($type, $sortOrder);

        $itemProviderMock = $this->createMock(ItemProviderInterface::class);
        $this->itemProviderFactoryMock->expects($this->once())
            ->method('create')
            ->with($type, $sortOrder)
            ->willReturn($itemProviderMock);

        $filterDataProviderMock = $this->createMock(FilterDataProviderInterface::class);
        $filterDataProviderMock->expects($this->once())
            ->method('getData')
            ->with($filterMock, $attribute)
            ->willReturn($filterData);
        $this->filterDataProviderPoolMock->expects($this->once())
            ->method('getDataProvider')
            ->with($type)
            ->willReturn($filterDataProviderMock);

        $dataToCreate = [
            'itemsProvider' => $itemProviderMock,
            'data' => array_merge($filterData, [LayerFilter::LAYER => $layerMock])
        ];

        $layerFilterMock = $this->createMock(LayerFilter::class);
        $this->objectManagerMock->expects($this->once())
            ->method('create')
            ->with(LayerFilter::class, $dataToCreate)
            ->willReturn($layerFilterMock);

        $this->assertSame($layerFilterMock, $this->model->create($filterMock, $layerMock, $attribute));
    }

    /**
     * @return array
     */
    public function createDataProvider()
    {
        return [
            ['attribute' => null],
            ['attribute' => $this->createMock(Attribute::class)],
        ];
    }

    /**
     * Test create method if no items data provider found
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testCreateNoItemsDataProvider()
    {
        $type = 'filter_type';
        $sortOrder = 'asc';

        $layerMock = $this->createMock(Layer::class);
        $filterMock = $this->getFilterMock($type, $sortOrder);

        $this->itemProviderFactoryMock->expects($this->once())
            ->method('create')
            ->with($type, $sortOrder)
            ->willThrowException(new \Exception('Error!'));

        $this->filterDataProviderPoolMock->expects($this->never())
            ->method('getDataProvider');

        $this->objectManagerMock->expects($this->never())
            ->method('create');

        $this->model->create($filterMock, $layerMock, null);
    }

    /**
     * Test create method if no filter data provider found
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testCreateNoFilterDataProvider()
    {
        $type = 'filter_type';
        $sortOrder = 'asc';

        $layerMock = $this->createMock(Layer::class);
        $filterMock = $this->getFilterMock($type, $sortOrder);

        $itemProviderMock = $this->createMock(ItemProviderInterface::class);
        $this->itemProviderFactoryMock->expects($this->once())
            ->method('create')
            ->with($type, $sortOrder)
            ->willReturn($itemProviderMock);

        $this->filterDataProviderPoolMock->expects($this->once())
            ->method('getDataProvider')
            ->with($type)
            ->willThrowException(new \Exception('Error!'));

        $this->objectManagerMock->expects($this->never())
            ->method('create');

        $this->model->create($filterMock, $layerMock, null);
    }

    /**
     * Get filter mock
     *
     * @param string $type
     * @param string $sortOrder
     * @return FilterInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getFilterMock($type, $sortOrder)
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getType')
            ->willReturn($type);
        $filterMock->expects($this->any())
            ->method('getStorefrontSortOrder')
            ->willReturn($sortOrder);

        return $filterMock;
    }
}
