<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Filter\Image;

use Aheadworks\Layerednav\Model\Filter\Image\TitleResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Model\StorefrontValueResolver;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Test for \Aheadworks\Layerednav\Model\Filter\Image\TitleResolver
 */
class TitleResolverTest extends TestCase
{
    /**
     * @var TitleResolver
     */
    private $model;

    /**
     * @var StorefrontValueResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storefrontValueResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->storefrontValueResolverMock = $this->createMock(StorefrontValueResolver::class);

        $this->model = $objectManager->getObject(
            TitleResolver::class,
            [
                'storefrontValueResolver' => $this->storefrontValueResolverMock,
            ]
        );
    }

    /**
     * Test getCurrentImageStorefrontTitle method when storefront title for filter is not set
     *
     * @param int $storeId
     * @param string $imageStorefrontTitle
     * @param array $filterTitles
     * @param string $filterDefaultTitle
     * @param string $filterStorefrontTitle
     * @param array $filterImageTitles
     *
     * @dataProvider getCurrentImageStorefrontTitleFilterStorefrontTitleIsNotSetDataProvider
     */
    public function testGetCurrentImageStorefrontTitleFilterStorefrontTitleIsNotSet(
        $storeId,
        $imageStorefrontTitle,
        $filterTitles,
        $filterDefaultTitle,
        $filterStorefrontTitle,
        $filterImageTitles
    ) {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getStorefrontTitle')
            ->willReturn('');
        $filterMock->expects($this->any())
            ->method('getDefaultTitle')
            ->willReturn($filterDefaultTitle);

        $filterMock->expects($this->any())
            ->method('getStorefrontTitles')
            ->willReturn($filterTitles);

        $filterMock->expects($this->any())
            ->method('getImageTitles')
            ->willReturn($filterImageTitles);

        $this->storefrontValueResolverMock->expects($this->exactly(2))
            ->method('getStorefrontValue')
            ->willReturnMap(
                [
                    [
                        $filterTitles,
                        $storeId,
                        $filterDefaultTitle,
                        $filterStorefrontTitle,
                    ],
                    [
                        $filterImageTitles,
                        $storeId,
                        $filterStorefrontTitle,
                        $imageStorefrontTitle,
                    ],
                ]
            );

        $this->assertEquals(
            $imageStorefrontTitle,
            $this->model->getCurrentImageStorefrontTitle(
                $filterMock,
                $storeId
            )
        );
    }

    /**
     * @return array
     */
    public function getCurrentImageStorefrontTitleFilterStorefrontTitleIsNotSetDataProvider()
    {
        $firstFilterTitle = $this->getStoreValueMock(1, 'filter title first store');
        $secondFilterTitle = $this->getStoreValueMock(2, 'filter title second store');

        $firstImageTitle = $this->getStoreValueMock(1, 'image title first store');
        $secondImageTitle = $this->getStoreValueMock(2, 'image title second store');

        $filterDefaultTitle = 'filter default title';

        return [
            [
                'storeId' => 2,
                'imageStorefrontTitle' => 'image title second store',
                'filterTitles' => [
                    $firstFilterTitle,
                ],
                'filterDefaultTitle' => $filterDefaultTitle,
                'filterStorefrontTitle' => $filterDefaultTitle,
                'filterImageTitles' => [
                    $firstImageTitle,
                    $secondImageTitle
                ],
            ],
            [
                'storeId' => 1,
                'imageStorefrontTitle' => 'image title first store',
                'filterTitles' => [
                    $firstFilterTitle,
                ],
                'filterDefaultTitle' => $filterDefaultTitle,
                'filterStorefrontTitle' => 'filter title first store',
                'filterImageTitles' => [
                    $firstImageTitle,
                    $secondImageTitle
                ],
            ],
            [
                'storeId' => 1,
                'imageStorefrontTitle' => 'filter title first store',
                'filterTitles' => [
                    $firstFilterTitle,
                ],
                'filterDefaultTitle' => $filterDefaultTitle,
                'filterStorefrontTitle' => 'filter title first store',
                'filterImageTitles' => [
                    $secondImageTitle,
                ],
            ],
        ];
    }

    /**
     * Test getCurrentImageStorefrontTitle method when storefront title for filter is set
     *
     * @param int $storeId
     * @param string $imageStorefrontTitle
     * @param array $filterTitles
     * @param string $filterDefaultTitle
     * @param string $filterStorefrontTitle
     * @param array $filterImageTitles
     *
     * @dataProvider getCurrentImageStorefrontTitleFilterStorefrontTitleIsSetDataProvider
     */
    public function testGetCurrentImageStorefrontTitleFilterStorefrontTitleIsSet(
        $storeId,
        $imageStorefrontTitle,
        $filterTitles,
        $filterDefaultTitle,
        $filterStorefrontTitle,
        $filterImageTitles
    ) {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getStorefrontTitle')
            ->willReturn($filterStorefrontTitle);
        $filterMock->expects($this->any())
            ->method('getDefaultTitle')
            ->willReturn($filterDefaultTitle);

        $filterMock->expects($this->any())
            ->method('getStorefrontTitles')
            ->willReturn($filterTitles);

        $filterMock->expects($this->any())
            ->method('getImageTitles')
            ->willReturn($filterImageTitles);

        $this->storefrontValueResolverMock->expects($this->once())
            ->method('getStorefrontValue')
            ->with($filterImageTitles, $storeId, $filterStorefrontTitle)
            ->willReturn($imageStorefrontTitle);

        $this->assertEquals(
            $imageStorefrontTitle,
            $this->model->getCurrentImageStorefrontTitle(
                $filterMock,
                $storeId
            )
        );
    }

    /**
     * @return array
     */
    public function getCurrentImageStorefrontTitleFilterStorefrontTitleIsSetDataProvider()
    {
        $firstFilterTitle = $this->getStoreValueMock(1, 'filter title first store');
        $secondFilterTitle = $this->getStoreValueMock(2, 'filter title second store');

        $firstImageTitle = $this->getStoreValueMock(1, 'image title first store');
        $secondImageTitle = $this->getStoreValueMock(2, 'image title second store');

        $filterDefaultTitle = 'filter default title';

        return [
            [
                'storeId' => 2,
                'imageStorefrontTitle' => 'image title second store',
                'filterTitles' => [
                    $firstFilterTitle,
                ],
                'filterDefaultTitle' => $filterDefaultTitle,
                'filterStorefrontTitle' => $filterDefaultTitle,
                'filterImageTitles' => [
                    $firstImageTitle,
                    $secondImageTitle
                ],
            ],
            [
                'storeId' => 1,
                'imageStorefrontTitle' => 'image title first store',
                'filterTitles' => [
                    $firstFilterTitle,
                ],
                'filterDefaultTitle' => $filterDefaultTitle,
                'filterStorefrontTitle' => 'filter title first store',
                'filterImageTitles' => [
                    $firstImageTitle,
                    $secondImageTitle
                ],
            ],
            [
                'storeId' => 1,
                'imageStorefrontTitle' => 'filter title first store',
                'filterTitles' => [
                    $firstFilterTitle,
                ],
                'filterDefaultTitle' => $filterDefaultTitle,
                'filterStorefrontTitle' => 'filter title first store',
                'filterImageTitles' => [
                    $secondImageTitle,
                ],
            ],
        ];
    }

    /**
     * Retrieve store value mock object
     *
     * @param int $storeId
     * @param string $value
     * @return StoreValueInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getStoreValueMock($storeId, $value)
    {
        $storeValueMock = $this->createMock(StoreValueInterface::class);

        $storeValueMock->expects($this->any())
            ->method('getStoreId')
            ->willReturn($storeId);
        $storeValueMock->expects($this->any())
            ->method('getValue')
            ->willReturn($value);

        return $storeValueMock;
    }
}
