<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Ui\Component\Modifier;

use Aheadworks\Layerednav\Ui\Component\Modifier\Category;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;

/**
 * Class CategoryTest
 *
 * @package Aheadworks\Layerednav\Test\Unit\Ui\Component\Modifier
 */
class CategoryTest extends TestCase
{
    /**
     * @var Category
     */
    private $modifier;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->modifier = $objectManager->getObject(
            Category::class,
            []
        );
    }

    /**
     * Test modifyMeta method
     *
     * @param array $meta
     * @param array $result
     * @dataProvider modifyMetaDataProvider
     */
    public function testModifyMeta($meta, $result)
    {
        $this->assertSame($result, $this->modifier->modifyMeta($meta));
    }

    /**
     * @return array
     */
    public function modifyMetaDataProvider()
    {
        return [
            [
                [],
                []
            ],
            [
                ['some meta data'],
                ['some meta data'],
            ],
        ];
    }

    /**
     * Test modifyData method
     *
     * @param array $data
     * @param array $result
     * @dataProvider modifyDataDataProvider
     */
    public function testModifyData($data, $result)
    {
        $this->assertSame($result, $this->modifier->modifyData($data));
    }

    /**
     * @return array
     */
    public function modifyDataDataProvider()
    {
        return [
            [
                [
                    FilterInterface::TYPE => FilterInterface::CATEGORY_FILTER,
                    FilterInterface::CATEGORY_MODE => FilterInterface::CATEGORY_MODE_ALL,
                    FilterInterface::EXCLUDE_CATEGORY_IDS => [],
                    'category_filter_data' => [
                        'list_styles' => [
                            [
                                'store_id' => '1',
                                'value' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
                            ],
                        ],
                    ],
                    'store_id' => 1,
                ],
                [

                    'type' => FilterInterface::CATEGORY_FILTER,
                    'category_mode' => FilterInterface::CATEGORY_MODE_ALL,
                    'exclude_category_ids' => [],
                    'category_filter_data' => [
                        'list_styles' => [
                            [
                                'store_id' => '1',
                                'value' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
                            ],
                        ],
                    ],
                    'store_id' => 1,
                    'category_list_styles' => [
                        [
                            'store_id' => '1',
                            'value' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
                        ]
                    ],
                    'default_category_list_style' => '0',
                    'category_list_style' => FilterCategoryInterface::CATEGORY_STYLE_SINGLE_PATH,
                ]
            ],
        ];
    }
}
