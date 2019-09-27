<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Source\Filter;

use Aheadworks\Layerednav\Model\Source\Filter\CategoryListStyles as FilterCategoryListStyles;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Source\Filter\CategoryListStyles
 */
class CategoryListStylesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterCategoryListStyles
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
            FilterCategoryListStyles::class,
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
        $type = FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH;
        $label = __('Single path');

        $this->assertEquals($label, $this->model->getOptionByValue($type));
    }
}
