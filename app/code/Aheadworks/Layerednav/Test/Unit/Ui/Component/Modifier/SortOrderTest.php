<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Ui\Component\Modifier;

use Aheadworks\Layerednav\Ui\Component\Modifier\SortOrder;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class SortOrderTest
 *
 * @package Aheadworks\Layerednav\Test\Unit\Ui\Component\Modifier
 */
class SortOrderTest extends TestCase
{
    /**
     * @var SortOrder
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
            SortOrder::class,
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
                    FilterInterface::SORT_ORDERS =>
                        [
                            [
                                StoreValueInterface::STORE_ID => 1,
                                StoreValueInterface::VALUE => FilterInterface::SORT_ORDER_MANUAL,
                            ],
                        ],
                    'store_id' => 1,
                ],
                [

                    'type' => FilterInterface::CATEGORY_FILTER,
                    'sort_orders' =>
                        [
                            [
                                'store_id' => 1,
                                'value' => FilterInterface::SORT_ORDER_MANUAL,
                            ]
                        ],
                    'store_id' => 1,
                    'default_sort_order' => '0',
                    'sort_order' => FilterInterface::SORT_ORDER_MANUAL,
                ]
            ],
        ];
    }
}
