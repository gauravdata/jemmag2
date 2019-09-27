<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\DisplayState;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\DisplayState
 */
class DisplayStateTest extends TestCase
{
    /**
     * @var DisplayState
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
            DisplayState::class,
            []
        );
    }

    /**
     * Test process method
     *
     * @param $postData
     * @param $processedData
     * @dataProvider processDataProvider
     */
    public function testProcess($postData, $processedData)
    {
        $this->assertEquals($processedData, $this->model->process($postData));
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return [
            [
                'postData' => [
                    'store_id' => '1',
                    'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
                    'default_display_state' => '0',
                ],
                'processedData' => [
                    'store_id' => '1',
                    'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
                    'default_display_state' => '0',
                    'display_states' => [
                        [
                            'store_id' => '1',
                            'value' => FilterInterface::DISPLAY_STATE_COLLAPSED
                        ]
                    ],
                ],
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
                    'default_display_state' => '1',
                ],
                'processedData' => [
                    'store_id' => '1',
                    'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
                    'default_display_state' => '1',
                ],
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
                    'default_display_state' => '1',
                    'display_states' => [
                        0 => [
                            'store_id' => '1',
                            'value' => FilterInterface::DISPLAY_STATE_COLLAPSED
                        ],
                        1 => [
                            'store_id' => '2',
                            'value' => FilterInterface::DISPLAY_STATE_EXPANDED
                        ],
                    ],
                ],
                'processedData' => [
                    'store_id' => '1',
                    'display_state' => FilterInterface::DISPLAY_STATE_COLLAPSED,
                    'default_display_state' => '1',
                    'display_states' => [
                        1 => [
                            'store_id' => '2',
                            'value' => FilterInterface::DISPLAY_STATE_EXPANDED
                        ],
                    ],
                ],
            ],
        ];
    }
}
