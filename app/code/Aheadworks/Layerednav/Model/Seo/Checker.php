<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Seo;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\PageTypeResolver;

/**
 * Class Checker
 *
 * @package Aheadworks\Layerednav\Model\Seo
 */
class Checker
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @param Config $config
     * @param PageTypeResolver $pageTypeResolver
     */
    public function __construct(
        Config $config,
        PageTypeResolver $pageTypeResolver
    ) {
        $this->config = $config;
        $this->pageTypeResolver = $pageTypeResolver;
    }

    /**
     * Check if need to use text representation of corresponding values
     *
     * @return bool
     */
    public function isNeedToUseTextValues()
    {
        return $this->isNeedToUseUseAttributeValueInsteadOfId() || $this->isNeedToUseSubcategoriesAsAttributeValues();
    }

    /**
     * Check if need to use attribute value instead of id
     *
     * @return bool
     */
    public function isNeedToUseUseAttributeValueInsteadOfId()
    {
        return $this->config->getSeoFriendlyUrlOption() == SeoFriendlyUrl::ATTRIBUTE_VALUE_INSTEAD_OF_ID
            && !($this->pageTypeResolver->isSearchPage());
    }

    /**
     * Check if need to use attribute values as subcategories
     *
     * @return bool
     */
    public function isNeedToUseSubcategoriesAsAttributeValues()
    {
        return $this->config->getSeoFriendlyUrlOption() == SeoFriendlyUrl::ATTRIBUTE_VALUE_AS_SUBCATEGORY
            && !($this->pageTypeResolver->isSearchPage());
    }
}
