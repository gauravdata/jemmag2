<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Image\View;

use Aheadworks\Layerednav\Model\Image\View\DataConverter;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Aheadworks\Layerednav\Model\Image\ViewInterfaceFactory as ImageViewInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Test for \Aheadworks\Layerednav\Model\Image\View\DataConverter
 */
class DataConverterTest extends TestCase
{
    /**
     * @var DataConverter
     */
    private $model;

    /**
     * @var ImageViewInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageViewFactoryMock;

    /**
     * @var DataObjectProcessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectProcessorMock;

    /**
     * @var DataObjectHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dataObjectHelperMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->imageViewFactoryMock = $this->createMock(ImageViewInterfaceFactory::class);
        $this->dataObjectProcessorMock = $this->createMock(DataObjectProcessor::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);

        $this->model = $objectManager->getObject(
            DataConverter::class,
            [
                'imageViewFactory' => $this->imageViewFactoryMock,
                'dataObjectProcessor' => $this->dataObjectProcessorMock,
                'dataObjectHelper' => $this->dataObjectHelperMock,
            ]
        );
    }

    /**
     * Test dataArrayToModel method
     */
    public function testDataArrayToModel()
    {
        $data = [
            ImageViewInterface::ID => 1,
        ];

        $imageViewMock = $this->createMock(ImageViewInterface::class);

        $this->imageViewFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageViewMock);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($imageViewMock, $data, ImageViewInterface::class);

        $this->assertSame($imageViewMock, $this->model->dataArrayToModel($data));
    }

    /**
     * Test modelToDataArray method
     */
    public function testModelToDataArray()
    {
        $imageViewMock = $this->createMock(ImageViewInterface::class);
        $data = [
            ImageViewInterface::ID => 1,
        ];

        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($imageViewMock, ImageViewInterface::class)
            ->willReturn($data);

        $this->assertEquals($data, $this->model->modelToDataArray($imageViewMock));
    }
}
