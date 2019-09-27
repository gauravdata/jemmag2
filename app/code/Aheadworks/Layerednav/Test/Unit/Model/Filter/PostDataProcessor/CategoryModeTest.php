<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\CategoryMode;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\CategoryMode
 */
class CategoryModeTest extends TestCase
{
    /**
     * @var CategoryMode
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
            CategoryMode::class,
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
                    'category_mode' => FilterInterface::CATEGORY_MODE_ALL,
                ],
                'processedData' => [
                    'store_id' => '1',
                    'category_mode' => FilterInterface::CATEGORY_MODE_ALL,
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'category_mode' => FilterInterface::CATEGORY_MODE_LOWEST_LEVEL,
                ],
                'processedData' => [
                    'store_id' => '1',
                    'category_mode' => FilterInterface::CATEGORY_MODE_LOWEST_LEVEL,
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'category_mode' => FilterInterface::CATEGORY_MODE_EXCLUDE,
                ],
                'processedData' => [
                    'store_id' => '1',
                    'category_mode' => FilterInterface::CATEGORY_MODE_EXCLUDE,
                    'exclude_category_ids' => [],
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'category_mode' => FilterInterface::CATEGORY_MODE_EXCLUDE,
                    'exclude_category_ids' => [25]
                ],
                'processedData' => [
                    'store_id' => '1',
                    'category_mode' => FilterInterface::CATEGORY_MODE_EXCLUDE,
                    'exclude_category_ids' => [25],
                ]
            ],
        ];
    }
}
