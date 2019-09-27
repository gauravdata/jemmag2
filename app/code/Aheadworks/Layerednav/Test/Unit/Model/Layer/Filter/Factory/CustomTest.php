<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Factory;

use Aheadworks\Layerednav\Model\Layer\Filter\Factory\Custom;
use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\DisplayStateResolver;
use Aheadworks\Layerednav\Model\Filter\ModeResolver as FilterModeResolver;
use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Aheadworks\Layerednav\Model\Layer\Filter as LayerFilter;
use Aheadworks\Layerednav\Model\Layer\Filter\Factory\ImageViewDataResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Factory\Custom
 */
class CustomTest extends TestCase
{
    /**
     * @var Custom
     */
    private $model;

    /**
     * @var ImageViewDataResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageViewDataResolverMock;

    /**
     * @var DisplayStateResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $displayStateResolverMock;

    /**
     * @var FilterModeResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filterModeResolverMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->imageViewDataResolverMock = $this->createMock(ImageViewDataResolver::class);
        $this->displayStateResolverMock = $this->createMock(DisplayStateResolver::class);
        $this->filterModeResolverMock = $this->createMock(FilterModeResolver::class);

        $this->model = $objectManager->getObject(
            Custom::class,
            [
                'imageViewDataResolver' => $this->imageViewDataResolverMock,
                'displayStateResolver' => $this->displayStateResolverMock,
                'filterModeResolver' => $this->filterModeResolverMock
            ]
        );
    }

    /**
     * Test getData method
     */
    public function testGetData()
    {
        $code = 'code';
        $title = 'title';
        $type = 'title';
        $displayState = 123;
        $mode = 'single-select';
        $swatchesViewMode = 2;
        $attribute = null;

        $filterMock = $this->getFilterMock($code, $title, $type, $swatchesViewMode);

        $imageViewMock = $this->createMock(ImageViewInterface::class);
        $this->imageViewDataResolverMock->expects($this->once())
            ->method('getImageView')
            ->with($filterMock)
            ->willReturn($imageViewMock);

        $this->displayStateResolverMock->expects($this->once())
            ->method('getStorefrontDisplayState')
            ->with($filterMock)
            ->willReturn($displayState);

        $this->filterModeResolverMock->expects($this->once())
            ->method('getStorefrontFilterMode')
            ->with($filterMock)
            ->willReturn($mode);

        $expectedResult = [
            LayerFilter::CODE => $code,
            LayerFilter::TITLE => $title,
            LayerFilter::TYPE => $type,
            LayerFilter::IMAGE => $imageViewMock,
            LayerFilter::ATTRIBUTE => $attribute,
            LayerFilter::ADDITIONAL_DATA => [
                FilterInterface::STOREFRONT_DISPLAY_STATE => $displayState,
                ModeInterface::STOREFRONT_FILTER_MODE => $mode,
                FilterInterface::IMAGE_STOREFRONT_TITLE => '',
                FilterInterface::SWATCHES_VIEW_MODE => $swatchesViewMode,
            ]
        ];

        $this->assertEquals($expectedResult, $this->model->getData($filterMock, $attribute));
    }

    /**
     * Test getData method if an error with image occurs
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testGetDataImageError()
    {
        $code = 'code';
        $title = 'title';
        $type = 'title';
        $swatchesViewMode = 2;
        $attribute = null;

        $filterMock = $this->getFilterMock($code, $title, $type, $swatchesViewMode);

        $this->imageViewDataResolverMock->expects($this->once())
            ->method('getImageView')
            ->with($filterMock)
            ->willThrowException(new \Exception('Error!'));

        $this->model->getData($filterMock, $attribute);
    }

    /**
     * Get filter mock
     *
     * @param string $code
     * @param string $title
     * @param string $type
     * @param int $swatchesViewMode
     * @return FilterInterface|\PHPUnit\Framework\MockObject\MockObject
     * @throws \ReflectionException
     */
    private function getFilterMock($code, $title, $type, $swatchesViewMode)
    {
        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->any())
            ->method('getCode')
            ->willReturn($code);
        $filterMock->expects($this->any())
            ->method('getStorefrontTitle')
            ->willReturn($title);
        $filterMock->expects($this->any())
            ->method('getType')
            ->willReturn($type);
        $filterMock->expects($this->any())
            ->method('getType')
            ->willReturn($type);
        $filterMock->expects($this->any())
            ->method('getSwatchesViewMode')
            ->willReturn($swatchesViewMode);

        return $filterMock;
    }
}
