<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\PostDataProcessor;

use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\PostDataProcessor\CategoryFilter;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\PostDataProcessor\CategoryFilter
 */
class CategoryFilterTest extends TestCase
{
    /**
     * @var CategoryFilter
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
            CategoryFilter::class,
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
                    'type' => FilterInterface::ATTRIBUTE_FILTER,
                ],
                'processedData' => [
                    'store_id' => '1',
                    'type' => FilterInterface::ATTRIBUTE_FILTER,
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'type' => FilterInterface::CATEGORY_FILTER,
                    'category_list_style' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH
                ],
                'processedData' => [
                    'store_id' => '1',
                    'type' => FilterInterface::CATEGORY_FILTER,
                    'category_filter_data' => [
                        'list_styles' => [
                            [
                                'store_id' => '1',
                                'value' => 'single_path'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'postData' => [
                    'store_id' => '1',
                    'type' => FilterInterface::CATEGORY_FILTER,
                    'category_list_style' => FilterCategoryInterface::CATEGORY_STYLE_DEFAULT
                ],
                'processedData' => [
                    'store_id' => '1',
                    'type' => FilterInterface::CATEGORY_FILTER,
                    'category_filter_data' => [
                        'list_styles' => [
                            [
                                'store_id' => '1',
                                'value' => 'default'
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
