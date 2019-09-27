<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Test\Unit\Model\File\Uploader;

use Aheadworks\Layerednav\Model\File\Uploader\Postprocessor;
use PHPUnit\Framework\TestCase;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Aheadworks\Layerednav\Model\File\Info;
use Magento\Framework\Exception\LocalizedException;

/**
 * Test for \Aheadworks\Layerednav\Model\File\Uploader\Postprocessor
 */
class PostprocessorTest extends TestCase
{
    /**
     * @var Postprocessor
     */
    private $model;

    /**
     * @var Info|\PHPUnit_Framework_MockObject_MockObject
     */
    private $infoMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->infoMock = $this->createMock(Info::class);

        $this->model = $objectManager->getObject(
            Postprocessor::class,
            [
                'info' => $this->infoMock,
            ]
        );
    }

    /**
     * Test execute method when file is not saved
     *
     * @param array $data
     * @dataProvider executeFileIsNotSavedDataProvider
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage File is not saved
     */
    public function testExecuteFileIsNotSaved($data)
    {
        $this->infoMock->expects($this->never())
            ->method('getMediaUrl');
        $this->infoMock->expects($this->never())
            ->method('getBaseMediaUrl');

        $this->model->execute($data);
    }

    /**
     * @return array
     */
    public function executeFileIsNotSavedDataProvider()
    {
        return [
            [
                'data' => [],
            ],
            [
                'data' => [
                    'name' => 'Screenshotfrom20190617104043.png',
                ],
            ],
            [
                'data' => [
                    'name' => 'Screenshotfrom20190617104043.png',
                    'type' => 'image/png',
                    'tmp_name' => '/tmp/phpLnkEaa',
                    'error' => 0,
                    'size' => 81794,
                    'path' => '/var/www/html/pub/media/aw_ln/media',
                ],
            ],
        ];
    }

    /**
     * Test execute method when fetching media url causes exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error!
     */
    public function testExecuteMediaUrlException()
    {
        $data = [
            'name' => 'Screenshotfrom20190617104043.png',
            'type' => 'image/png',
            'tmp_name' => '/tmp/phpLnkEaa',
            'error' => 0,
            'size' => 81794,
            'path' => '/var/www/html/pub/media/aw_ln/media',
            'file' => 'Screenshotfrom20190617104043_7.png',
        ];

        $this->infoMock->expects($this->once())
            ->method('getMediaUrl')
            ->with($data['file'])
            ->willThrowException(new LocalizedException(__('Error!')));
        $this->infoMock->expects($this->never())
            ->method('getBaseMediaUrl');

        $this->model->execute($data);
    }

    /**
     * Test execute method when fetching base media url causes exception
     *
     * @expectedException \Magento\Framework\Exception\LocalizedException
     * @expectedExceptionMessage Error!
     */
    public function testExecuteBaseMediaUrlException()
    {
        $data = [
            'name' => 'Screenshotfrom20190617104043.png',
            'type' => 'image/png',
            'tmp_name' => '/tmp/phpLnkEaa',
            'error' => 0,
            'size' => 81794,
            'path' => '/var/www/html/pub/media/aw_ln/media',
            'file' => 'Screenshotfrom20190617104043_7.png',
        ];

        $this->infoMock->expects($this->once())
            ->method('getMediaUrl')
            ->with($data['file'])
            ->willReturn('http://www.store.com/media/aw_ln/media/Screenshotfrom20190617104043_7.png');
        $this->infoMock->expects($this->once())
            ->method('getBaseMediaUrl')
            ->willThrowException(new LocalizedException(__('Error!')));

        $this->model->execute($data);
    }

    /**
     * Test execute method
     */
    public function testExecute()
    {
        $data = [
            'name' => 'Screenshotfrom20190617104043.png',
            'type' => 'image/png',
            'tmp_name' => '/tmp/phpLnkEaa',
            'error' => 0,
            'size' => 81794,
            'path' => '/var/www/html/pub/media/aw_ln/media',
            'file' => 'Screenshotfrom20190617104043_7.png',
        ];

        $mediaUrl = 'http://www.store.com/media/aw_ln/media/Screenshotfrom20190617104043_7.png';
        $baseMediaUrl = 'http://www.store.com/media/aw_ln/media/';

        $this->infoMock->expects($this->once())
            ->method('getMediaUrl')
            ->with($data['file'])
            ->willReturn($mediaUrl);
        $this->infoMock->expects($this->once())
            ->method('getBaseMediaUrl')
            ->willReturn($baseMediaUrl);

        $result = $this->model->execute($data);
        $this->assertTrue(is_array($result));
        $this->assertTrue(isset($result['file']));
        $this->assertTrue(isset($result['size']));
        $this->assertTrue(isset($result['name']));
        $this->assertTrue(isset($result['path']));
        $this->assertTrue(isset($result['type']));
        $this->assertTrue(isset($result['url']));
        $this->assertTrue(isset($result['base_url']));
        $this->assertTrue(isset($result['file_name']));
        $this->assertTrue(isset($result['id']));
    }
}
