<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\File;

use Aheadworks\Layerednav\Model\File\Info;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\File\Mime;
use Magento\Store\Model\Store;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Test for \Aheadworks\Layerednav\Model\File\Info
 */
class InfoTest extends TestCase
{
    /**
     * @var Info
     */
    private $model;

    /**
     * @var Filesystem|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesystemMock;

    /**
     * @var StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    /**
     * @var WriteInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mediaDirectoryMock;

    /**
     * @var Mime|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileMimeMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->filesystemMock = $this->createMock(Filesystem::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->fileMimeMock = $this->createMock(Mime::class);

        $this->model = $objectManager->getObject(
            Info::class,
            [
                'storeManager' => $this->storeManagerMock,
                'filesystem' => $this->filesystemMock,
                'fileMime' => $this->fileMimeMock,
            ]
        );
    }

    /**
     * Test getBaseMediaUrl method
     */
    public function testGetBaseMediaUrl()
    {
        $storeBaseUrl = 'www.store.com/media';
        $baseMediaUrl = $storeBaseUrl . 'aw_ln/media/';
        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn($storeBaseUrl);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->assertEquals($baseMediaUrl, $this->model->getBaseMediaUrl());
    }

    /**
     * Test getBaseMediaUrl method with exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetBaseMediaUrlException()
    {
        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $this->model->getBaseMediaUrl();
    }

    /**
     * Test getMediaUrl method with exception
     *
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetMediaUrlException()
    {
        $file = 'test1.jpg';

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willThrowException(new NoSuchEntityException());

        $this->model->getMediaUrl($file);
    }

    /**
     * Test getMediaUrl method
     */
    public function testGetMediaUrl()
    {
        $file = 'test1.jpg';

        $storeBaseUrl = 'www.store.com/media';
        $mediaUrl = $storeBaseUrl . 'aw_ln/media/' . $file;
        $storeMock = $this->createMock(Store::class);
        $storeMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(UrlInterface::URL_TYPE_MEDIA)
            ->willReturn($storeBaseUrl);

        $this->storeManagerMock->expects($this->once())
            ->method('getStore')
            ->willReturn($storeMock);

        $this->assertEquals($mediaUrl, $this->model->getMediaUrl($file));
    }

    /**
     * Test getMediaDirectory method
     */
    public function testGetMediaDirectory()
    {
        $mediaDirectoryMock = $this->createMock(WriteInterface::class);
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($mediaDirectoryMock);

        $this->assertSame($mediaDirectoryMock, $this->model->getMediaDirectory());
        $this->assertSame($mediaDirectoryMock, $this->model->getMediaDirectory());
    }

    /**
     * Test getMediaDirectory method with exception
     *
     * @expectedException \Magento\Framework\Exception\FileSystemException
     */
    public function testGetMediaDirectoryException()
    {
        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willThrowException(new \Magento\Framework\Exception\FileSystemException(__('Error!')));

        $this->model->getMediaDirectory();
    }

    /**
     * Test getStatisticsData method with exception
     *
     * @expectedException \Magento\Framework\Exception\FileSystemException
     */
    public function testGetStatisticsDataException()
    {
        $fileName = 'file1.jpg';

        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willThrowException(new \Magento\Framework\Exception\FileSystemException(__('Error!')));

        $this->model->getStatisticsData($fileName);
    }

    /**
     * Test getStatisticsData method
     */
    public function testGetStatisticsData()
    {
        $fileName = 'file1.jpg';
        $statisticsData = [];

        $mediaDirectoryMock = $this->createMock(WriteInterface::class);
        $mediaDirectoryMock->expects($this->once())
            ->method('stat')
            ->with('aw_ln/media/' . $fileName)
            ->willReturn($statisticsData);

        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($mediaDirectoryMock);

        $this->assertSame($statisticsData, $this->model->getStatisticsData($fileName));
    }

    /**
     * Test getMimeType method with exception
     *
     * @expectedException \Magento\Framework\Exception\FileSystemException
     */
    public function testGetMimeTypeException()
    {
        $fileName = 'file1.jpg';

        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willThrowException(new \Magento\Framework\Exception\FileSystemException(__('Error!')));

        $this->model->getMimeType($fileName);
    }

    /**
     * Test getMimeType method
     */
    public function testGetMimeType()
    {
        $fileName = 'file1.jpg';
        $fullPath = 'pub/media/aw_ln/media/' . $fileName;
        $mimeType = 'image/jpg';

        $mediaDirectoryMock = $this->createMock(WriteInterface::class);
        $mediaDirectoryMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with('aw_ln/media/' . $fileName)
            ->willReturn($fullPath);

        $this->filesystemMock->expects($this->once())
            ->method('getDirectoryWrite')
            ->with(DirectoryList::MEDIA)
            ->willReturn($mediaDirectoryMock);

        $this->fileMimeMock->expects($this->once())
            ->method('getMimeType')
            ->with($fullPath)
            ->willReturn($mimeType);

        $this->assertEquals($mimeType, $this->model->getMimeType($fileName));
    }
}
