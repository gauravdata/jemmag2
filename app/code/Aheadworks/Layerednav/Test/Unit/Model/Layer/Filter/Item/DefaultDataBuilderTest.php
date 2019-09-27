<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Item;

use Aheadworks\Layerednav\Model\Layer\Filter\Item\DefaultDataBuilder;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Item\DefaultDataBuilder
 */
class DefaultDataBuilderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var DefaultDataBuilder
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
            DefaultDataBuilder::class,
            []
        );
    }

    /**
     * Test build method
     */
    public function testBuild()
    {
        $itemOneLabel = 'Item A';
        $itemOneValue = 1;
        $itemOneCount = 10;

        $itemTwoLabel = 'Item B';
        $itemTwoValue = 2;
        $itemTwoCount = 20;

        $result = [
            [
                'label' => $itemOneLabel,
                'value' => $itemOneValue,
                'count' => $itemOneCount,
                'imageData' => [],
            ],
            [
                'label' => $itemTwoLabel,
                'value' => $itemTwoValue,
                'count' => $itemTwoCount,
                'imageData' => [],
            ],
        ];

        $this->model->addItemData($itemOneLabel, $itemOneValue, $itemOneCount);
        $this->model->addItemData($itemTwoLabel, $itemTwoValue, $itemTwoCount);

        $this->assertEquals($result, $this->model->build());
    }
}
