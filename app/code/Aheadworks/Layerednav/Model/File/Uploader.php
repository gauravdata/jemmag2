<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\File;

use Magento\MediaStorage\Model\File\Uploader as FileUploader;
use Magento\MediaStorage\Model\File\UploaderFactory as FileUploaderFactory;
use Aheadworks\Layerednav\Model\File\Uploader\Postprocessor as FileUploaderPostprocessor;

/**
 * Class Uploader
 *
 * @package Aheadworks\Layerednav\Model\File
 */
class Uploader
{
    /**
     * @var FileUploaderFactory
     */
    private $fileUploaderFactory;

    /**
     * @var string[]
     */
    private $allowedExtensions;

    /**
     * @var Info
     */
    private $info;

    /**
     * @var FileUploaderPostprocessor
     */
    private $fileUploaderPostprocessor;

    /**
     * @param FileUploaderFactory $fileUploaderFactory
     * @param Info $info
     * @param FileUploaderPostprocessor $fileUploaderPostprocessor
     * @param array $allowedExtensions
     */
    public function __construct(
        FileUploaderFactory $fileUploaderFactory,
        Info $info,
        FileUploaderPostprocessor $fileUploaderPostprocessor,
        array $allowedExtensions = []
    ) {
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->info = $info;
        $this->fileUploaderPostprocessor = $fileUploaderPostprocessor;
        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * Save file to the temporary folder
     *
     * @param string $fileId
     * @return array
     */
    public function saveToTmpFolder($fileId)
    {
        try {
            $mediaDirectory = $this->info
                ->getMediaDirectory()
                ->getAbsolutePath(Info::FILE_DIR);
            /** @var FileUploader $fileUploader */
            $fileUploader = $this->fileUploaderFactory->create(['fileId' => $fileId]);
            $fileUploader
                ->setAllowRenameFiles(true)
                ->setAllowedExtensions($this->getAllowedExtensions());
            $data = $fileUploader->save($mediaDirectory);
            $result = $this->fileUploaderPostprocessor->execute($data);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $result;
    }

    /**
     * Set allowed extensions
     *
     * @param string[] $allowedExtensions
     * @return $this
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    /**
     * Retrieve allowed extensions
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }
}
