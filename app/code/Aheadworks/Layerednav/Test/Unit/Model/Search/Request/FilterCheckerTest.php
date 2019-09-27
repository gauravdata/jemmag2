<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Search\Request;

use Aheadworks\Layerednav\Model\Search\Request\FilterChecker;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Search\Request\FilterChecker
 */
class FilterCheckerTest extends TestCase
{
    /**
     * @var FilterChecker
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

        $this->model = $objectManager->getObject(FilterChecker::class, []);
    }

    /**
     * Test isCustom method
     */
    public function testIsCustom()
    {
        $this->setProperty('customFilters', ['custom_filter']);

        $customFilterMock = $this->getFilterMock('custom_filter');
        $notCustomFilterMock = $this->getFilterMock('other_filter');

        $this->assertTrue($this->model->isCustom($customFilterMock));
        $this->assertFalse($this->model->isCustom($notCustomFilterMock));
    }

    /**
     * Test isBaseCategory method
     */
    public function testIsBaseCategory()
    {
        $this->setProperty('baseCategoryFilter', 'category_filter');

        $baseCategoryFilterMock = $this->getFilterMock('category_filter');
        $categoryFilterMock = $this->getFilterMock('category_query_filter');

        $this->assertTrue($this->model->isBaseCategory($baseCategoryFilterMock));
        $this->assertFalse($this->model->isBaseCategory($categoryFilterMock));
    }

    /**
     * Test isCategory method
     */
    public function testIsCategory()
    {
        $this->setProperty('categoryFilter', 'category_query_filter');

        $baseCategoryFilterMock = $this->getFilterMock('category_filter');
        $categoryFilterMock = $this->getFilterMock('category_query_filter');

        $this->assertFalse($this->model->isCategory($baseCategoryFilterMock));
        $this->assertTrue($this->model->isCategory($categoryFilterMock));
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

    /**
     * @param $name
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getFilterMock($name)
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getName')
            ->willReturn($name);
        return $filterMock;
    }
}
