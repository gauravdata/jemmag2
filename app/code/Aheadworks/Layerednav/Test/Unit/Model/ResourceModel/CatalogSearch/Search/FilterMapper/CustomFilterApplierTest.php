<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\ResourceModel\CatalogSearch\Search\FilterMapper;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\Context;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\FilterApplierInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier
 */
class CustomFilterApplierTest extends TestCase
{
    /**
     * @var CustomFilterApplier
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

        $this->model = $objectManager->getObject(CustomFilterApplier::class, []);
    }

    /**
     * Test apply method
     */
    public function testApply()
    {
        $filterName = 'filter_name';

        $contextMock = $this->createMock(Context::class);
        $filterMock = $this->getFilterMock($filterName);
        $selectMock = $this->createMock(Select::class);

        $filterApplierMock = $this->createMock(FilterApplierInterface::class);
        $filterApplierMock->expects($this->once())
            ->method('apply')
            ->with($contextMock, $filterMock, $selectMock)
            ->willReturn($selectMock);

        $appliers = [$filterName => $filterApplierMock];
        $this->setProperty('appliers', $appliers);

        $this->assertSame($selectMock, $this->model->apply($contextMock, $filterMock, $selectMock));
    }

    /**
     * Test apply method if no applier is defined
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Filter applier for filter_name is not defined
     */
    public function testApplyNoFilterApplier()
    {
        $filterName = 'filter_name';

        $contextMock = $this->createMock(Context::class);
        $filterMock = $this->getFilterMock($filterName);
        $selectMock = $this->createMock(Select::class);

        $this->model->apply($contextMock, $filterMock, $selectMock);
    }

    /**
     * Test apply method if bad filter applied is defined
     *
     * @expectedException \Exception
     */
    public function testApplyBadFilterApplier()
    {
        $filterName = 'filter_name';

        $contextMock = $this->createMock(Context::class);
        $filterMock = $this->getFilterMock($filterName);
        $selectMock = $this->createMock(Select::class);

        $filterApplierMock = $this->createMock(DataObject::class);

        $appliers = [$filterName => $filterApplierMock];
        $this->setProperty('appliers', $appliers);

        $this->model->apply($contextMock, $filterMock, $selectMock);
    }

    /**
     * Get filter mock
     *
     * @param string $name
     * @return FilterInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getFilterMock($name)
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);

        return $filterMock;
    }

    /**
     * Set property
     *
     * @param string $propertyName
     * @param mixed $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($propertyName, $value)
    {
        $class = new \ReflectionClass($this->model);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($this->model, $value);

        return $this;
    }
}
