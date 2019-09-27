<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Source\Filter;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class SwatchesMode
 *
 * @package Aheadworks\Layerednav\Model\Source\Filter
 */
class SwatchesMode implements OptionSourceInterface
{
    /**#@+
     * Option values
     */
    const TITLE_ONLY = 1;
    const IMAGE_AND_TITLE = 2;
    const IMAGE_ONLY = 3;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::TITLE_ONLY,
                'label' => __('Show value names only')
            ],
            [
                'value' => self::IMAGE_AND_TITLE,
                'label' => __('Show images and value')
            ],
            [
                'value' => self::IMAGE_ONLY,
                'label' => __('Show images only')
            ],
        ];
    }
}
