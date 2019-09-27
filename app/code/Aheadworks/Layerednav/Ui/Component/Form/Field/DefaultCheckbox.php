<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Form\Field;

use Aheadworks\Layerednav\Ui\FilterDataProvider;
use Magento\Ui\Component\Form\Field as FormField;
use Magento\Store\Model\Store;

/**
 * Class DefaultCheckbox
 * @package Aheadworks\Layerednav\Ui\Component\Form\Field
 */
class DefaultCheckbox extends FormField
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        /** @var FilterDataProvider $dataProvider */
        $dataProvider = $this->getContext()->getDataProvider();
        if ($dataProvider) {
            $data = $dataProvider->getData();
            $filterData = reset($data);
            if (isset($filterData['store_id']) && $filterData['store_id'] == Store::DEFAULT_STORE_ID) {
                $config = $this->getConfig();
                $config['visible'] = false;
                $config['value'] = 0;
                $this->setConfig($config);
            }
        }
        parent::prepare();
    }
}
