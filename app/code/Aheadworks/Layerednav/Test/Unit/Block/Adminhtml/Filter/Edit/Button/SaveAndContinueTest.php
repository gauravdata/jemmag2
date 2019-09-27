<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Block\Adminhtml\Filter\Edit\Button;

use Aheadworks\Layerednav\Block\Adminhtml\Filter\Edit\Button\SaveAndContinue as SaveAndContinueButton;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Block\Adminhtml\Filter\Edit\Button\SaveAndContinue
 */
class SaveAndContinueTest extends TestCase
{
    /**
     * @var SaveAndContinueButton
     */
    private $button;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->button = $objectManager->getObject(
            SaveAndContinueButton::class,
            []
        );
    }

    /**
     * Test getButtonData method
     */
    public function testGetButtonData()
    {
        $this->assertTrue(is_array($this->button->getButtonData()));
    }
}
