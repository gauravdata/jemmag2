<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Controller\Adminhtml\Filter\Swatch;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\File\Uploader as FileUploader;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Response\HttpInterface;

/**
 * Class Upload
 *
 * @package Aheadworks\Layerednav\Controller\Adminhtml\Filter\Swatch
 */
class Upload extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Layerednav::filters';

    /**
     * @var string
     */
    const FILE_ID = 'datafile';

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param Context $context
     * @param FileUploader $fileUploader
     * @param Config $config
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        FileUploader $fileUploader,
        Config $config,
        SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->fileUploader = $fileUploader;
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * Image upload action
     *
     * @return string
     */
    public function execute()
    {
        try {
            $fileData = $this->fileUploader
                ->setAllowedExtensions($this->config->getAllowedExtensionsForFilterImage())
                ->saveToTmpFolder(self::FILE_ID);
            $result = [
                'swatch_path' => isset($fileData['base_url']) ? $fileData['base_url'] : '',
                'file_path' => isset($fileData['file_name']) ? $fileData['file_name'] : '',
            ];
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            ];
        }

        /** @var HttpInterface $response */
        $response = $this->getResponse();
        return $response->setBody($this->serializer->serialize($result));
    }
}
