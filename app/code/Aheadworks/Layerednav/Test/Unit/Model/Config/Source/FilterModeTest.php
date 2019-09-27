<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Config\Source;

use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface as FilterModeInterface;
use Aheadworks\Layerednav\Model\Config\Source\FilterMode;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Config\Source\FilterMode
 */
class FilterModeTest extends TestCase
{
    /**
     * @var FilterMode
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
            FilterMode::class,
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
        $type = FilterModeInterface::MODE_SINGLE_SELECT;
        $label = __('Single Select');

        $this->assertEquals($label, $this->model->getOptionByValue($type));
    }
}
