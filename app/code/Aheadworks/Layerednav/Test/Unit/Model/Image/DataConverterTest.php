<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Image;

use Aheadworks\Layerednav\Model\Image\DataConverter;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Api\Data\ImageInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Test for \Aheadworks\Layerednav\Model\Image\DataConverter
 */
class DataConverterTest extends TestCase
{
    /**
     * @var DataConverter
     */
    private $model;

    /**
     * @var ImageInterfaceFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $imageFactoryMock;

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

        $this->imageFactoryMock = $this->createMock(ImageInterfaceFactory::class);
        $this->dataObjectProcessorMock = $this->createMock(DataObjectProcessor::class);
        $this->dataObjectHelperMock = $this->createMock(DataObjectHelper::class);

        $this->model = $objectManager->getObject(
            DataConverter::class,
            [
                'imageFactory' => $this->imageFactoryMock,
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
            ImageInterface::ID => 1,
            ImageInterface::NAME => 'test_name',
            ImageInterface::FILE_NAME => 'test_name.jpg',
        ];

        $imageMock = $this->createMock(ImageInterface::class);

        $this->imageFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($imageMock);

        $this->dataObjectHelperMock->expects($this->once())
            ->method('populateWithArray')
            ->with($imageMock, $data, ImageInterface::class);

        $this->assertSame($imageMock, $this->model->dataArrayToModel($data));
    }

    /**
     * Test modelToDataArray method
     */
    public function testModelToDataArray()
    {
        $imageMock = $this->createMock(ImageInterface::class);
        $data = [
            ImageInterface::ID => 1,
            ImageInterface::NAME => 'test_name',
            ImageInterface::FILE_NAME => 'test_name.jpg',
        ];

        $this->dataObjectProcessorMock->expects($this->once())
            ->method('buildOutputDataArray')
            ->with($imageMock, ImageInterface::class)
            ->willReturn($data);

        $this->assertEquals($data, $this->model->modelToDataArray($imageMock));
    }
}
