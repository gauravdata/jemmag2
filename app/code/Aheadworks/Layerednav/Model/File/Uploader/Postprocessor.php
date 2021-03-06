<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\File\Uploader;

use Aheadworks\Layerednav\Model\File\Info;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Math\Random;

/**
 * Class Postprocessor
 *
 * @package Aheadworks\Layerednav\Model\File\Uploader
 */
class Postprocessor
{
    /**
     * Max value for autogenerated image id value
     */
    const MAX_VALUE_OF_IMAGE_ID = 2147483648;

    /**
     * @var Info
     */
    private $info;

    /**
     * @param Info $info
     */
    public function __construct(
        Info $info
    ) {
        $this->info = $info;
    }

    /**
     * Postprocessing for data, retrieved after file uploading
     *
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function execute($data)
    {
        $baseData = [
            'file' => '',
            'size' => '',
            'name' => '',
            'path' => '',
            'type' => ''
        ];

        $preparedData = array_intersect_key($data, $baseData);

        if (isset($preparedData['file'])) {
            $preparedData['url'] = $this->info->getMediaUrl($preparedData['file']);
            $preparedData['base_url'] = $this->info->getBaseMediaUrl();
            $preparedData['file_name'] = $preparedData['file'];
            $preparedData['id'] = Random::getRandomNumber(0, self::MAX_VALUE_OF_IMAGE_ID);
        } else {
            throw new LocalizedException(__('File is not saved'));
        }

        return $preparedData;
    }
}
