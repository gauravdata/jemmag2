<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Source\Filter;

use Aheadworks\Layerednav\Model\Source\Filter\SortOrders as FilterSortOrders;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Source\Filter\SortOrders
 */
class SortOrdersTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterSortOrders
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

        $this->model = $objectManager->getObject(
            FilterSortOrders::class,
            []
        );
    }

    /**
     * Test toOptionArray method
     */
    public function testToOptionArray()
    {
        $this->assertTrue(is_array($this->model->toOptionArray()));
    }

    /**
     * Test getOptions method
     */
    public function testGetOptions()
    {
        $this->assertTrue(is_array($this->model->getOptions()));
    }

    /**
     * Test getOptionByValue method
     */
    public function testGetOptionByValue()
    {
        $type = FilterInterface::SORT_ORDER_ASC;
        $label = __('A-Z');

        $this->assertEquals($label, $this->model->getOptionByValue($type));
    }
}
