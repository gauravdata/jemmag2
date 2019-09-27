<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Ui\Component\Listing\Column\Store\Options as StoreOptions;

/**
 * Class Stores
 * @package Aheadworks\Followupemail2\Model\Source
 * @codeCoverageIgnore
 */
class Stores extends StoreOptions implements OptionSourceInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $allStores = [
            'label' => __('All Store Views'),
            'value' => 0
        ];
        $this->currentOptions['All Store Views'] = $allStores;

        return parent::toOptionArray();
    }
}
