<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Ui\Component\Modifier;

use Aheadworks\Layerednav\Ui\Component\Modifier\Title;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\FilterInterface;

/**
 * Class TitleTest
 *
 * @package Aheadworks\Layerednav\Test\Unit\Ui\Component\Modifier
 */
class TitleTest extends TestCase
{
    /**
     * @var Title
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
            Title::class,
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
                    FilterInterface::DEFAULT_TITLE => 'Test title',
                    FilterInterface::STOREFRONT_TITLES => [],
                    'store_id' => 1,
                ],
                [

                    'type' => FilterInterface::CATEGORY_FILTER,
                    'default_title' => 'Test title',
                    'storefront_titles' => [],
                    'store_id' => 1,
                    'title' => 'Test title',
                    'default_title_checkbox' => '1',
                ]
            ],
        ];
    }
}
