<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Form;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Ui\FilterDataProvider;
use Magento\Ui\Component\Container;

/**
 * Class SortOrderContainer
 * @package Aheadworks\Layerednav\Ui\Component\Form
 */
class SortOrderContainer extends Container
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

            if (isset($filterData[FilterInterface::TYPE])
                && $filterData[FilterInterface::TYPE] != FilterInterface::ATTRIBUTE_FILTER
            ) {
                $config = $this->getConfig();
                $config['visible'] = false;
                $this->setConfig($config);
            }
        }
        parent::prepare();
    }
}
