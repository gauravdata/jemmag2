<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Test\Unit\Model\Statistics\EmailTracker;

use Aheadworks\Followupemail2\Model\Statistics\EmailTracker\Encryptor;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Test for \Aheadworks\Followupemail2\Model\Statistics\EmailTracker\Encryptor
 */
class EncryptorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Encryptor
     */
    private $model;

    /**
     * @var EncryptorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $encryptorMock;

    /**
     * Init mocks for tests
     *
     * @return void
     */
    public function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->encryptorMock = $this->getMockBuilder(EncryptorInterface::class)
            ->getMockForAbstractClass();

        $this->model = $objectManager->getObject(
            Encryptor::class,
            [
                'encryptor' => $this->encryptorMock,
            ]
        );
    }

    /**
     * Test encrypt-decrypt sequence
     */
    public function testEncryptDecrypt()
    {
        $params = [
            'param1' => 1,
            'param2' => 2,
        ];
        $encryptedParams = 'ABCDEFG1234567890';

        $this->encryptorMock->expects($this->once())
            ->method('encrypt')
            ->with(serialize($params))
            ->willReturn($encryptedParams);
        $this->encryptorMock->expects($this->once())
            ->method('decrypt')
            ->with($encryptedParams)
            ->willReturn(serialize($params));

        $this->assertEquals($params, $this->model->decrypt($this->model->encrypt($params)));
    }
}
