<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Form\Field;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Ui\FilterDataProvider;

/**
 * Class IsFilterableField
 * @package Aheadworks\Layerednav\Ui\Component\Form\Field
 */
class IsFilterableField extends GlobalScopeField
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $config = $this->getConfig();
        /** @var FilterDataProvider $dataProvider */
        $dataProvider = $this->getContext()->getDataProvider();
        if ($dataProvider) {
            $data = $dataProvider->getData();
            $filterData = reset($data);

            if (isset($filterData[FilterInterface::TYPE])
                && in_array($filterData[FilterInterface::TYPE], FilterInterface::CUSTOM_FILTER_TYPES)
                && $filterData[FilterInterface::TYPE] != FilterInterface::CATEGORY_FILTER
            ) {
                $config['disabled'] = true;
            }
        }

        $this->setConfig($config);
        parent::prepare();
    }
}
