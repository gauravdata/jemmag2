<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\Image;

use Aheadworks\Layerednav\Model\StorefrontValueResolver;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class TitleResolver
 *
 * @package Aheadworks\Layerednav\Model\Filter\Image
 */
class TitleResolver
{
    /**
     * @var StorefrontValueResolver
     */
    private $storefrontValueResolver;

    /**
     * @param StorefrontValueResolver $storefrontValueResolver
     */
    public function __construct(
        StorefrontValueResolver $storefrontValueResolver
    ) {
        $this->storefrontValueResolver = $storefrontValueResolver;
    }

    /**
     * Retrieve current storefront title for filter image
     *
     * @param FilterInterface $filter
     * @param int $storeId
     * @return string
     */
    public function getCurrentImageStorefrontTitle($filter, $storeId)
    {
        $filterStorefrontTitle = empty($filter->getStorefrontTitle())
            ? $this->storefrontValueResolver->getStorefrontValue(
                $filter->getStorefrontTitles(),
                $storeId,
                $filter->getDefaultTitle()
            ) : $filter->getStorefrontTitle();

        $imageStorefrontTitle = $this->storefrontValueResolver->getStorefrontValue(
            $filter->getImageTitles(),
            $storeId,
            $filterStorefrontTitle
        );

        return $imageStorefrontTitle;
    }
}
