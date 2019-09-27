<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Ui\Component\Modifier;

use Aheadworks\Layerednav\Ui\Component\Modifier\Image;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Image\Resolver as ImageResolver;

/**
 * Class ImageTest
 *
 * @package Aheadworks\Layerednav\Test\Unit\Ui\Component\Modifier
 */
class ImageTest extends TestCase
{
    /**
     * @var Image
     */
    private $modifier;

    /**
     * @var ImageResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->imageResolverMock = $this->createMock(ImageResolver::class);

        $this->modifier = $objectManager->getObject(
            Image::class,
            [
                'imageResolver' => $this->imageResolverMock
            ]
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
                    'store_id' => 1,
                ],
                [

                    'type' => FilterInterface::CATEGORY_FILTER,
                    'store_id' => 1,
                    'image' => [],
                ]
            ],
        ];
    }
}
