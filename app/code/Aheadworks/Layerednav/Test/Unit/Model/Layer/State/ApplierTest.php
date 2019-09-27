<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\State;

use Aheadworks\Layerednav\Model\Layer\FilterInterface;
use Aheadworks\Layerednav\Model\Layer\State\Applier;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;
use Aheadworks\Layerednav\Model\Layer\Filter\ItemInterface as FilterItemInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Catalog\Model\Layer;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\State\Applier
 */
class ApplierTest extends TestCase
{
    /**
     * @var Applier
     */
    private $model;

    /**
     * @var LayerState|\PHPUnit_Framework_MockObject_MockObject
     */
    private $layerStateMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->layerStateMock = $this->createMock(LayerState::class);

        $this->model = $objectManager->getObject(
            Applier::class,
            [
                'layerState' => $this->layerStateMock,
            ]
        );
    }

    /**
     * Test add method
     */
    public function testAdd()
    {
        $field = 'field';
        $condition = ['condition'];
        $orOption = true;

        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with($field, $condition)
            ->willReturnSelf();

        $layerMock = $this->createMock(Layer::class);
        $layerMock->expects($this->any())
            ->method('getProductCollection')
            ->willReturn($collectionMock);

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getLayer')
            ->willReturn($layerMock);

        $filterItemOneMock = $this->getFilterItemMock($filterMock);
        $filterItemTwoMock = $this->getFilterItemMock($filterMock);
        $filterItems = [$filterItemOneMock, $filterItemTwoMock];

        $this->layerStateMock->expects($this->at(0))
            ->method('addFilter')
            ->with($filterItemOneMock, $field, $condition, $orOption)
            ->willReturnSelf();
        $this->layerStateMock->expects($this->at(1))
            ->method('addFilter')
            ->with($filterItemTwoMock, $field, $condition, $orOption)
            ->willReturnSelf();

        $this->assertSame($this->model, $this->model->add($filterItems, $field, $condition, $orOption));
    }

    /**
     * Test add method if no items specified
     */
    public function testAddNoItems()
    {
        $field = 'field';
        $condition = ['condition'];
        $orOption = true;

        $collectionMock = $this->createMock(Collection::class);
        $collectionMock->expects($this->never())
            ->method('addFieldToFilter');

        $this->layerStateMock->expects($this->never())
            ->method('addFilter');

        $filterItems = [];
        $this->assertSame($this->model, $this->model->add($filterItems, $field, $condition, $orOption));
    }

    /**
     * Get filter item mock
     *
     * @param FilterInterface|\PHPUnit\Framework\MockObject\MockObject $filterMock
     * @return FilterItemInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getFilterItemMock($filterMock)
    {
        $filterItemMock = $this->createMock(FilterItemInterface::class);
        $filterItemMock->expects($this->any())
            ->method('getFilter')
            ->willReturn($filterMock);

        return $filterItemMock;
    }
}
