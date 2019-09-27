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
 * Class CategoryListStyleContainer
 * @package Aheadworks\Layerednav\Ui\Component\Form
 */
class CategoryListStyleContainer extends Container
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
                && $filterData[FilterInterface::TYPE] == FilterInterface::CATEGORY_FILTER
            ) {
                $config = $this->getConfig();
                $config['visible'] = true;
                $this->setConfig($config);
            }
        }
        parent::prepare();
    }
}
