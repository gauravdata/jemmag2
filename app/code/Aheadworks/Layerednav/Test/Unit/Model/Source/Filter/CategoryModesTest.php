<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Source\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Source\Filter\CategoryModes as FilterCategoryModes;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Source\Filter\CategoryModes
 */
class CategoryModesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterCategoryModes
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
            FilterCategoryModes::class,
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
        $type = FilterInterface::CATEGORY_MODE_EXCLUDE;
        $label = __('Exclude specific categories');

        $this->assertEquals($label, $this->model->getOptionByValue($type));
    }
}
