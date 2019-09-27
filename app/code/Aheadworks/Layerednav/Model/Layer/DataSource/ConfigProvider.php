<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\DataSource;

use Aheadworks\Layerednav\Model\PageTypeResolver;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\UrlInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ConfigProvider
 * @package Aheadworks\Layerednav\Model\Layer\DataSource
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @var QueryFactory
     */
    private $searchQueryFactory;

    /**
     * @var Layer
     */
    private $layer;

    /**
     * @param UrlInterface $url
     * @param StoreManagerInterface $storeManager
     * @param PageTypeResolver $pageTypeResolver
     * @param QueryFactory $searchQueryFactory
     * @param LayerResolver $layerResolver
     */
    public function __construct(
        UrlInterface $url,
        StoreManagerInterface $storeManager,
        PageTypeResolver $pageTypeResolver,
        QueryFactory $searchQueryFactory,
        LayerResolver $layerResolver
    ) {
        $this->url = $url;
        $this->storeManager = $storeManager;
        $this->pageTypeResolver = $pageTypeResolver;
        $this->searchQueryFactory = $searchQueryFactory;
        $this->layer  = $layerResolver->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return [
            'url' => $this->getItemsCountUrl(),
            'pageType' => $this->pageTypeResolver->getType(),
            'categoryId' => $this->layer->getCurrentCategory()->getId(),
            'searchQueryText' => $this->getSearchQueryText()
        ];
    }

    /**
     * Get filter items count retrieve url
     *
     * @return string
     */
    private function getItemsCountUrl()
    {
        return $this->url->getUrl(
            'awlayerednav/ajax/itemsCount',
            ['_secure' => $this->storeManager->getStore()->isCurrentlySecure()]
        );
    }

    /**
     * Get search query text
     *
     * @return string
     */
    private function getSearchQueryText()
    {
        if ($this->pageTypeResolver->isSearchPage()) {
            return $this->searchQueryFactory->get()->getQueryText();
        }
        return '';
    }
}
