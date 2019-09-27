<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver;

use Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolverInterface;
use Magento\Swatches\Helper\Media as SwatchesMediaHelper;

/**
 * Class Image
 *
 * @package Aheadworks\Layerednav\ViewModel\Navigation\FilterRenderer\Swatches\DataResolver
 */
class Image implements DataResolverInterface
{
    /**
     * @var SwatchesMediaHelper
     */
    private $mediaHelper;

    /**
     * @var string
     */
    private $thumbImageType;

    /**
     * @var string
     */
    private $mainImageType;

    /**
     * @param SwatchesMediaHelper $mediaHelper
     * @param string $thumbImageType
     * @param string $mainImageType
     */
    public function __construct(
        SwatchesMediaHelper $mediaHelper,
        $thumbImageType = 'swatch_thumb',
        $mainImageType = 'swatch_image'
    ) {
        $this->mediaHelper = $mediaHelper;
        $this->thumbImageType = $thumbImageType;
        $this->mainImageType = $mainImageType;
    }

    /**
     * {@inheritdoc}
     */
    public function getClasses()
    {
        return [
            'image' => true,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTooltipValue($optionData)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTooltipThumb($optionData)
    {
        return $this->mediaHelper->getSwatchAttributeImage(
            $this->thumbImageType,
            isset($optionData['value']) ? $optionData['value'] : ''
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomStyle($optionData)
    {
        $imageUrl = $this->mediaHelper->getSwatchAttributeImage(
            $this->mainImageType,
            isset($optionData['value']) ? $optionData['value'] : ''
        );
        return "background: url(" . $imageUrl . ") no-repeat center;"
            . " background-size: initial;";
    }
}
