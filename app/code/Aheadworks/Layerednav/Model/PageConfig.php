<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model;

use Aheadworks\Layerednav\Model\Template\FilterProvider;
use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\Framework\View\Result\Page;
use Aheadworks\Layerednav\Model\Layer\Checker as LayerChecker;

/**
 * Class PageConfig
 * @package Aheadworks\Layerednav\Model
 */
class PageConfig
{
    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var FilterProvider
     */
    private $templateFilterProvider;

    /**
     * @var Toolbar
     */
    private $toolbar;

    /**
     * @var UrlManager
     */
    private $urlManager;

    /**
     * @var LayerChecker
     */
    private $layerChecker;

    /**
     * @param PageTypeResolver $pageTypeResolver
     * @param Config $config
     * @param FilterProvider $templateFilterProvider
     * @param Toolbar $toolbar
     * @param UrlManager $urlManager
     * @param LayerChecker $layerChecker
     */
    public function __construct(
        PageTypeResolver $pageTypeResolver,
        Config $config,
        FilterProvider $templateFilterProvider,
        Toolbar $toolbar,
        UrlManager $urlManager,
        LayerChecker $layerChecker
    ) {
        $this->pageTypeResolver = $pageTypeResolver;
        $this->config = $config;
        $this->templateFilterProvider = $templateFilterProvider;
        $this->toolbar = $toolbar;
        $this->urlManager = $urlManager;
        $this->layerChecker = $layerChecker;
    }

    /**
     * Apply layered navigation options to result page
     *
     * @param Page $page
     * @return void
     */
    public function apply(Page $page)
    {
        $pageConfig = $page->getConfig();
        $pageType = $this->pageTypeResolver->getType();
        if ($pageType == PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH
            && $this->config->isDisableIndexingOnCatalogSearch()
        ) {
            $pageConfig->setMetadata('robots', 'NOINDEX,FOLLOW');
        }
        if ($pageType == PageTypeResolver::PAGE_TYPE_CATEGORY) {
            $templateFilter = $this->templateFilterProvider->getFilter();
            $pageMetaDescriptionTemplate = $this->config->getPageMetaDescriptionTemplate();
            if (!empty($pageMetaDescriptionTemplate)) {
                $pageConfig->setMetadata(
                    'description',
                    $templateFilter->filter($pageMetaDescriptionTemplate)
                );
            }

            if ($this->config->isRewriteMetaRobotsTagEnabled()
                && ($this->toolbar->getCurrentPage() > 1
                    || $this->toolbar->getDirection()
                    || $this->toolbar->getLimit()
                    || $this->toolbar->getMode()
                    || $this->toolbar->getOrder()
                    || $this->hasMultipleFilterSelection()
                )
            ) {
                $pageConfig->setMetadata('robots', 'NOINDEX,NOFOLLOW');
            }

            if ($this->config->isAddCanonicalUrlsEnabled()) {
                $pageConfig->addRemotePageAsset(
                    $this->urlManager->getCurrentCanonicalUrl(),
                    'canonical',
                    ['attributes' => ['rel' => 'canonical']]
                );
            }
        }
    }

    /**
     * Check if there is a multiple filter selection
     *
     * @return bool
     */
    private function hasMultipleFilterSelection()
    {
        return $this->layerChecker->hasActiveFilterWithFewValues();
    }
}
