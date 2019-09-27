<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Source\Filter;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Source\Filter\Types as FilterTypes;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Source\Filter\Types
 */
class TypesTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FilterTypes
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
            FilterTypes::class,
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
        $type = FilterInterface::CATEGORY_FILTER;
        $label = __('Category');

        $this->assertEquals($label, $this->model->getOptionByValue($type));
    }
}
