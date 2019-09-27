<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Model\Statistics\EmailTracker;

use Magento\Framework\Encryption\EncryptorInterface;

/**
 * Class Encryptor
 * @package Aheadworks\Followupemail2\Model\Statistics\EmailTracker
 */
class Encryptor
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
    }

    /**
     * Encrypt tracking params
     *
     * @param array $params
     * @return string
     */
    public function encrypt($params)
    {
        return base64_encode($this->encryptor->encrypt(serialize($params)));
    }

    /**
     * Decrypt tracking key
     *
     * @param string $key
     * @return array
     */
    public function decrypt($key)
    {
        $serializedParams = $this->encryptor->decrypt(base64_decode($key));
        return unserialize($serializedParams);
    }
}
