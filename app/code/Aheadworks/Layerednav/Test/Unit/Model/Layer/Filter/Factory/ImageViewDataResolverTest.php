<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Layer\Filter\Factory;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Model\Image\DataConverter as ImageDataConverter;
use Aheadworks\Layerednav\Model\Image\Resolver as ImageResolver;
use Aheadworks\Layerednav\Model\Image\View\DataConverter as ImageViewDataConverter;
use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Factory\ImageViewDataResolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

/**
 * Test for \Aheadworks\Layerednav\Model\Layer\Filter\Factory\ImageViewDataResolver
 */
class ImageViewDataResolverTest extends TestCase
{
    /**
     * @var ImageViewDataResolver
     */
    private $model;

    /**
     * @var ImageResolver|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageResolverMock;

    /**
     * @var ImageDataConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageDataConverterMock;

    /**
     * @var ImageViewDataConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageViewDataConverterMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->imageResolverMock = $this->createMock(ImageResolver::class);
        $this->imageDataConverterMock = $this->createMock(ImageDataConverter::class);
        $this->imageViewDataConverterMock = $this->createMock(ImageViewDataConverter::class);

        $this->model = $objectManager->getObject(
            ImageViewDataResolver::class,
            [
                'imageResolver' => $this->imageResolverMock,
                'imageDataConverter' => $this->imageDataConverterMock,
                'imageViewDataConverter' => $this->imageViewDataConverterMock
            ]
        );
    }

    /**
     * Test getImageView method
     *
     * @param ImageInterface|\PHPUnit_Framework_MockObject_MockObject|null $image
     * @param ImageViewInterface|\PHPUnit_Framework_MockObject_MockObject|null $imageView
     * @param $expectedResult
     * @dataProvider getImageViewDataProvider
     * @throws \Exception
     */
    public function testGetImageView($image, $imageView, $expectedResult)
    {
        $imageData = ['image-data'];
        $imageViewData = ['image-view-data'];

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getImage')
            ->willReturn($image);

        $this->imageDataConverterMock->expects($this->any())
            ->method('modelToDataArray')
            ->with($image)
            ->willReturn($imageData);

        $this->imageResolverMock->expects($this->any())
            ->method('getViewData')
            ->with($imageData)
            ->willReturn($imageViewData);

        $this->imageViewDataConverterMock->expects($this->any())
            ->method('dataArrayToModel')
            ->with($imageViewData)
            ->willReturn($imageView);

        $this->assertEquals($expectedResult, $this->model->getImageView($filterMock));
    }

    /**
     * @return array
     */
    public function getImageViewDataProvider()
    {
        $imageViewMock = $this->createMock(ImageViewInterface::class);
        return [
            [
                'image' => $this->getImageMock('test.jpg'),
                'imageView' => $imageViewMock,
                'expectedResult' => $imageViewMock
            ],
            [
                'image' => $this->getImageMock(''),
                'imageView' => $imageViewMock,
                'expectedResult' => null
            ],
            [
                'image' => null,
                'imageView' => $imageViewMock,
                'expectedResult' => null
            ],
        ];
    }

    /**
     * Test getImageView method if an error occurs
     *
     * @expectedException \Exception
     * @expectedExceptionMessage Error!
     */
    public function testGetImageViewError()
    {
        $imageData = ['image-data'];
        $imageMock = $this->getImageMock('test.jpg');

        $filterMock = $this->createMock(FilterInterface::class);
        $filterMock->expects($this->once())
            ->method('getImage')
            ->willReturn($imageMock);

        $this->imageDataConverterMock->expects($this->once())
            ->method('modelToDataArray')
            ->with($imageMock)
            ->willReturn($imageData);

        $this->imageResolverMock->expects($this->any())
            ->method('getViewData')
            ->with($imageData)
            ->willThrowException(new \Exception(__('Error!')));

        $this->model->getImageView($filterMock);
    }

    /**
     * Get image mock
     *
     * @param string $filename
     * @return ImageInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getImageMock($filename)
    {
        $imageMock = $this->createMock(ImageInterface::class);
        $imageMock->expects($this->any())
            ->method('getFileName')
            ->willReturn($filename);

        return $imageMock;
    }
}
