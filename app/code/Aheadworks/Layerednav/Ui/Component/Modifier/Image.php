<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Image\Resolver as ImageResolver;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Image
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class Image implements ModifierInterface
{
    /**
     * @var ImageResolver
     */
    private $imageResolver;

    /**
     * @param ImageResolver $imageResolver
     */
    public function __construct(
        ImageResolver $imageResolver
    ) {
        $this->imageResolver = $imageResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $imageViewData = [];
        if (isset($data[FilterInterface::IMAGE])
            && !empty($data[FilterInterface::IMAGE])
        ) {
            try {
                $imageViewData[] = $this->imageResolver->getViewData($data[FilterInterface::IMAGE]);
            } catch (LocalizedException $exception) {
                $imageViewData = [];
            }
        }

        $data[FilterInterface::IMAGE] = $imageViewData;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
