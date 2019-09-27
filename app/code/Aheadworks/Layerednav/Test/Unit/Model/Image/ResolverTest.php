<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\Image;

use Aheadworks\Layerednav\Model\Image\Resolver;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Model\File\Info as FileInfo;
use Aheadworks\Layerednav\Api\Data\ImageInterface;
use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Test for \Aheadworks\Layerednav\Model\Image\Resolver
 */
class ResolverTest extends TestCase
{
    /**
     * @var Resolver
     */
    private $model;

    /**
     * @var FileInfo|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileInfoMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->fileInfoMock = $this->createMock(FileInfo::class);

        $this->model = $objectManager->getObject(
            Resolver::class,
            [
                'fileInfo' => $this->fileInfoMock,
            ]
        );
    }

    /**
     * Test getViewData method
     *
     * @param array $imageData
     * @param array $result
     * @throws FileSystemException
     * @throws NoSuchEntityException
     * @dataProvider getViewDataDataProvider
     */
    public function testGetViewData($imageData, $result)
    {
        $this->fileInfoMock->expects($this->any())
            ->method('getMediaUrl')
            ->willReturnMap(
                [
                    ['', ''],
                    ['test_filename_1.jpg', 'www.store.com/pub/media/test_filename_1.jpg'],
                    ['test_filename_2.png', 'www.store.com/pub/media/test_filename_2.png'],
                ]
            );

        $this->fileInfoMock->expects($this->any())
            ->method('getMimeType')
            ->willReturnMap(
                [
                    ['', ''],
                    ['test_filename_1.jpg', 'image/jpeg'],
                    ['test_filename_2.png', 'image/png'],
                ]
            );

        $this->fileInfoMock->expects($this->any())
            ->method('getStatisticsData')
            ->willReturnMap(
                [
                    ['', []],
                    ['test_filename_1.jpg', ['size' => 1024, 'uid' => 'test_uid']],
                    ['test_filename_2.png', ['uid' => 'test_uid']],
                ]
            );

        $this->assertEquals($result, $this->model->getViewData($imageData));
    }

    /**
     * @return array
     */
    public function getViewDataDataProvider()
    {
         return [
             [
                 'imageData' => [],
                 'result' => [
                     ImageViewInterface::ID => null,
                     ImageViewInterface::URL => '',
                     ImageViewInterface::TYPE => '',
                     ImageViewInterface::TITLE => '',
                     ImageViewInterface::NAME => '',
                     ImageViewInterface::FILE_NAME => '',
                     ImageViewInterface::SIZE => 0,
                 ],
             ],
             [
                 'imageData' => [
                     ImageInterface::ID => 12,
                 ],
                 'result' => [
                     ImageViewInterface::ID => 12,
                     ImageViewInterface::URL => '',
                     ImageViewInterface::TYPE => '',
                     ImageViewInterface::TITLE => '',
                     ImageViewInterface::NAME => '',
                     ImageViewInterface::FILE_NAME => '',
                     ImageViewInterface::SIZE => 0,
                 ],
             ],
             [
                 'imageData' => [
                     ImageInterface::NAME => 'test_name',
                 ],
                 'result' => [
                     ImageViewInterface::ID => null,
                     ImageViewInterface::URL => '',
                     ImageViewInterface::TYPE => '',
                     ImageViewInterface::TITLE => '',
                     ImageViewInterface::NAME => 'test_name',
                     ImageViewInterface::FILE_NAME => '',
                     ImageViewInterface::SIZE => 0,
                 ],
             ],
             [
                 'imageData' => [
                     ImageInterface::ID => 12,
                     ImageInterface::NAME => 'test_name',
                 ],
                 'result' => [
                     ImageViewInterface::ID => 12,
                     ImageViewInterface::URL => '',
                     ImageViewInterface::TYPE => '',
                     ImageViewInterface::TITLE => '',
                     ImageViewInterface::NAME => 'test_name',
                     ImageViewInterface::FILE_NAME => '',
                     ImageViewInterface::SIZE => 0,
                 ],
             ],
             [
                 'imageData' => [
                     ImageInterface::ID => 12,
                     ImageInterface::NAME => 'test_name',
                     ImageInterface::FILE_NAME => 'test_filename_1.jpg',
                 ],
                 'result' => [
                     ImageViewInterface::ID => 12,
                     ImageViewInterface::URL => 'www.store.com/pub/media/test_filename_1.jpg',
                     ImageViewInterface::TYPE => 'image/jpeg',
                     ImageViewInterface::TITLE => 'test_filename_1.jpg',
                     ImageViewInterface::NAME => 'test_name',
                     ImageViewInterface::FILE_NAME => 'test_filename_1.jpg',
                     ImageViewInterface::SIZE => 1024,
                 ],
             ],
             [
                 'imageData' => [
                     ImageInterface::ID => 15,
                     ImageInterface::NAME => 'test_name2',
                     ImageInterface::FILE_NAME => 'test_filename_2.png',
                 ],
                 'result' => [
                     ImageViewInterface::ID => 15,
                     ImageViewInterface::URL => 'www.store.com/pub/media/test_filename_2.png',
                     ImageViewInterface::TYPE => 'image/png',
                     ImageViewInterface::TITLE => 'test_filename_2.png',
                     ImageViewInterface::NAME => 'test_name2',
                     ImageViewInterface::FILE_NAME => 'test_filename_2.png',
                     ImageViewInterface::SIZE => 0,
                 ],
             ],
         ];
    }

    /**
     * Test getViewData method with exception on fetching url
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetViewDataUrlException()
    {
        $imageData = [
            ImageInterface::ID => 12,
            ImageInterface::NAME => 'test_name',
            ImageInterface::FILE_NAME => 'test_filename_1.jpg',
        ];

        $this->fileInfoMock->expects($this->once())
            ->method('getMediaUrl')
            ->willThrowException(new NoSuchEntityException());

        $this->fileInfoMock->expects($this->never())
            ->method('getMimeType');

        $this->fileInfoMock->expects($this->never())
            ->method('getStatisticsData');

        $this->model->getViewData($imageData);
    }

    /**
     * Test getViewData method with exception on fetching mime type
     *
     * @expectedException \Magento\Framework\Exception\FileSystemException
     * @expectedExceptionMessage Error!
     */
    public function testGetViewDataMimeTypeException()
    {
        $imageData = [
            ImageInterface::ID => 12,
            ImageInterface::NAME => 'test_name',
            ImageInterface::FILE_NAME => 'test_filename_1.jpg',
        ];

        $this->fileInfoMock->expects($this->once())
            ->method('getMediaUrl')
            ->with('test_filename_1.jpg')
            ->willReturn('www.store.com/pub/media/test_filename_1.jpg');

        $this->fileInfoMock->expects($this->once())
            ->method('getMimeType')
            ->willThrowException(new FileSystemException(__('Error!')));

        $this->fileInfoMock->expects($this->never())
            ->method('getStatisticsData');

        $this->model->getViewData($imageData);
    }

    /**
     * Test getViewData method with exception on fetching statistics data
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetViewDataStatisticsDataException()
    {
        $imageData = [
            ImageInterface::ID => 12,
            ImageInterface::NAME => 'test_name',
            ImageInterface::FILE_NAME => 'test_filename_1.jpg',
        ];

        $this->fileInfoMock->expects($this->once())
            ->method('getMediaUrl')
            ->with('test_filename_1.jpg')
            ->willReturn('www.store.com/pub/media/test_filename_1.jpg');

        $this->fileInfoMock->expects($this->once())
            ->method('getMimeType')
            ->with('test_filename_1.jpg')
            ->willReturn('image/jpeg');

        $this->fileInfoMock->expects($this->once())
            ->method('getStatisticsData')
            ->willThrowException(new NoSuchEntityException());

        $this->model->getViewData($imageData);
    }
}
