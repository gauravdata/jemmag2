<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\File;

use Aheadworks\Layerednav\Model\File\Uploader;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Aheadworks\Layerednav\Model\File\Info;
use Magento\MediaStorage\Model\File\Uploader as FileUploader;
use Magento\MediaStorage\Model\File\UploaderFactory as FileUploaderFactory;
use Aheadworks\Layerednav\Model\File\Uploader\Postprocessor as FileUploaderPostprocessor;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;

/**
 * Test for \Aheadworks\Layerednav\Model\File\Uploader
 */
class UploaderTest extends TestCase
{
    /**
     * @var Uploader
     */
    private $model;

    /**
     * @var FileUploaderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileUploaderFactoryMock;

    /**
     * @var Info|\PHPUnit_Framework_MockObject_MockObject
     */
    private $infoMock;

    /**
     * @var FileUploaderPostprocessor|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileUploaderPostprocessorMock;

    /**
     * @var string[]
     */
    private $allowedExtensions;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->fileUploaderFactoryMock = $this->createMock(FileUploaderFactory::class);
        $this->infoMock = $this->createMock(Info::class);
        $this->fileUploaderPostprocessorMock = $this->createMock(FileUploaderPostprocessor::class);
        $this->allowedExtensions = ['jpg', 'jpeg', 'png', 'bmp'];

        $this->model = $objectManager->getObject(
            Uploader::class,
            [
                'fileUploaderFactory' => $this->fileUploaderFactoryMock,
                'info' => $this->infoMock,
                'fileUploaderPostprocessor' => $this->fileUploaderPostprocessorMock,
                'allowedExtensions' => $this->allowedExtensions,
            ]
        );
    }

    /**
     * Test getAllowedExtensions method
     */
    public function testGetAllowedExtensions()
    {
        $this->assertTrue(is_array($this->model->getAllowedExtensions()));
    }

    /**
     * Test setAllowedExtensions method
     */
    public function testSetAllowedExtensions()
    {
        $allowedExtensions = ['txt'];
        $this->model->setAllowedExtensions($allowedExtensions);
        $this->assertEquals($allowedExtensions, $this->model->getAllowedExtensions());
    }

    /**
     * Test saveToTmpFolder method
     */
    public function testSaveToTmpFolder()
    {
        $fileId = 'datafile';
        $absolutePath = '/var/www/html/pub/media/aw_ln/media';
        $data = [
            'name' => 'Screenshotfrom20190617104043.png',
            'type' => 'image/png',
            'tmp_name' => '/tmp/phpLnkEaa',
            'error' => 0,
            'size' => 81794,
            'path' => '/var/www/html/pub/media/aw_ln/media',
            'file' => 'Screenshotfrom20190617104043_7.png',
        ];
        $result = [
            'name' => 'Screenshotfrom20190617104043.png',
            'type' => 'image/png',
            'size' => 81794,
            'path' => '/var/www/html/pub/media/aw_ln/media',
            'file' => 'Screenshotfrom20190617104043_7.png',
            'url' => 'http://www.store.com/media/aw_ln/media/Screenshotfrom20190617104043_7.png',
            'base_url' => 'http://www.store.com/media/aw_ln/media/',
            'file_name' => 'Screenshotfrom20190617104043_7.png',
            'id' => 519430824,
        ];

        $writeMock = $this->createMock(WriteInterface::class);
        $writeMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with(Info::FILE_DIR)
            ->willReturn($absolutePath);

        $this->infoMock->expects($this->once())
            ->method('getMediaDirectory')
            ->willReturn($writeMock);

        $fileUploaderMock = $this->createMock(FileUploader::class);
        $fileUploaderMock->expects($this->once())
            ->method('setAllowRenameFiles')
            ->with(true)
            ->willReturnSelf();
        $fileUploaderMock->expects($this->once())
            ->method('setAllowedExtensions')
            ->with($this->allowedExtensions)
            ->willReturnSelf();
        $fileUploaderMock->expects($this->once())
            ->method('save')
            ->with($absolutePath)
            ->willReturn($data);

        $this->fileUploaderFactoryMock->expects($this->once())
            ->method('create')
            ->with(['fileId' => $fileId])
            ->willReturn($fileUploaderMock);

        $this->fileUploaderPostprocessorMock->expects($this->once())
            ->method('execute')
            ->with($data)
            ->willReturn($result);

        $this->assertEquals($result, $this->model->saveToTmpFolder($fileId));
    }

    /**
     * Test saveToTmpFolder method with exception on fetching media directory object
     */
    public function testSaveToTmpFolderMediaDirectoryException()
    {
        $fileId = 'datafile';
        $errorMessage = __('Error!');
        $errorCode = 1;
        $result = [
            'error' => $errorMessage,
            'errorcode' => $errorCode,
        ];

        $exception = new FileSystemException($errorMessage, null, $errorCode);
        $this->infoMock->expects($this->once())
            ->method('getMediaDirectory')
            ->willThrowException($exception);

        $this->fileUploaderFactoryMock->expects($this->never())
            ->method('create');

        $this->fileUploaderPostprocessorMock->expects($this->never())
            ->method('execute');

        $this->assertEquals($result, $this->model->saveToTmpFolder($fileId));
    }

    /**
     * Test saveToTmpFolder method with exception on saving
     */
    public function testSaveToTmpFolderSavingException()
    {
        $fileId = 'datafile';
        $absolutePath = '/var/www/html/pub/media/aw_ln/media';
        $errorMessage = __('Error!');
        $errorCode = 1;
        $result = [
            'error' => $errorMessage,
            'errorcode' => $errorCode,
        ];

        $writeMock = $this->createMock(WriteInterface::class);
        $writeMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with(Info::FILE_DIR)
            ->willReturn($absolutePath);

        $this->infoMock->expects($this->once())
            ->method('getMediaDirectory')
            ->willReturn($writeMock);

        $fileUploaderMock = $this->createMock(FileUploader::class);
        $fileUploaderMock->expects($this->once())
            ->method('setAllowRenameFiles')
            ->with(true)
            ->willReturnSelf();
        $fileUploaderMock->expects($this->once())
            ->method('setAllowedExtensions')
            ->with($this->allowedExtensions)
            ->willReturnSelf();

        $exception = new \Exception($errorMessage, $errorCode);
        $fileUploaderMock->expects($this->once())
            ->method('save')
            ->with($absolutePath)
            ->willThrowException($exception);

        $this->fileUploaderFactoryMock->expects($this->once())
            ->method('create')
            ->with(['fileId' => $fileId])
            ->willReturn($fileUploaderMock);

        $this->fileUploaderPostprocessorMock->expects($this->never())
            ->method('execute');

        $this->assertEquals($result, $this->model->saveToTmpFolder($fileId));
    }

    /**
     * Test saveToTmpFolder method with exception on postprocessing
     */
    public function testSaveToTmpFolderPostprocessorException()
    {
        $fileId = 'datafile';
        $absolutePath = '/var/www/html/pub/media/aw_ln/media';
        $errorMessage = __('Error!');
        $errorCode = 1;
        $result = [
            'error' => $errorMessage,
            'errorcode' => $errorCode,
        ];
        $data = [
            'name' => 'Screenshotfrom20190617104043.png',
            'type' => 'image/png',
            'tmp_name' => '/tmp/phpLnkEaa',
            'error' => 0,
            'size' => 81794,
            'path' => '/var/www/html/pub/media/aw_ln/media',
            'file' => 'Screenshotfrom20190617104043_7.png',
        ];

        $writeMock = $this->createMock(WriteInterface::class);
        $writeMock->expects($this->once())
            ->method('getAbsolutePath')
            ->with(Info::FILE_DIR)
            ->willReturn($absolutePath);

        $this->infoMock->expects($this->once())
            ->method('getMediaDirectory')
            ->willReturn($writeMock);

        $fileUploaderMock = $this->createMock(FileUploader::class);
        $fileUploaderMock->expects($this->once())
            ->method('setAllowRenameFiles')
            ->with(true)
            ->willReturnSelf();
        $fileUploaderMock->expects($this->once())
            ->method('setAllowedExtensions')
            ->with($this->allowedExtensions)
            ->willReturnSelf();
        $fileUploaderMock->expects($this->once())
            ->method('save')
            ->with($absolutePath)
            ->willReturn($data);

        $this->fileUploaderFactoryMock->expects($this->once())
            ->method('create')
            ->with(['fileId' => $fileId])
            ->willReturn($fileUploaderMock);

        $exception = new LocalizedException($errorMessage, null, $errorCode);
        $this->fileUploaderPostprocessorMock->expects($this->once())
            ->method('execute')
            ->with($data)
            ->willThrowException($exception);

        $this->assertEquals($result, $this->model->saveToTmpFolder($fileId));
    }
}
