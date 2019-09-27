<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class SeoFriendlyUrl
 * @package Aheadworks\Layerednav\Model\Config\Source
 */
class SeoFriendlyUrl implements OptionSourceInterface
{
    /**
     * 'Default' option
     */
    const DEFAULT_OPTION = 'default';

    /**
     * 'Use attribute value instead of ID' option
     */
    const ATTRIBUTE_VALUE_INSTEAD_OF_ID = 'value_instead_of_id';

    /**
     * 'Use attributes to create URL subcategories' option
     */
    const ATTRIBUTE_VALUE_AS_SUBCATEGORY = 'value_as_subcategory';

    /**
     * @var array
     */
    private $options;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [
                [
                    'value' => self::DEFAULT_OPTION,
                    'label' => __('Default')
                ],
                [
                    'value' => self::ATTRIBUTE_VALUE_INSTEAD_OF_ID,
                    'label' => __('Use attribute value instead of ID')
                ],
                [
                    'value' => self::ATTRIBUTE_VALUE_AS_SUBCATEGORY,
                    'label' => __('Use attributes to create URL subcategories (Deprecated)')
                ]
            ];
        }
        return $this->options;
    }
}
