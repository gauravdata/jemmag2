<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request;

use Aheadworks\Layerednav\Api\Data\FilterInterface;

/**
 * Class ParamDataProvider
 * @package Aheadworks\Layerednav\App\Request
 */
class ParamDataProvider
{
    /**
     * Get custom filter param codes
     *
     * @return array
     */
    public function getCustomFilterParams()
    {
        return ['aw_stock', 'aw_sales', 'aw_new'];
    }

    /**
     * Get custom filter param values
     *
     * @return array
     */
    public function getCustomFilterParamValues()
    {
        return [
            'aw_stock' => 1,
            'aw_sales' => 1,
            'aw_new' => 1
        ];
    }

    /**
     * Get custom filter params seo friendly values
     *
     * @return array
     */
    public function getCustomFilterParamSeoFriendlyValues()
    {
        return [
            'aw_stock' => FilterInterface::STOCK_FILTER,
            'aw_sales' => FilterInterface::SALES_FILTER,
            'aw_new' => FilterInterface::NEW_FILTER
        ];
    }
}
