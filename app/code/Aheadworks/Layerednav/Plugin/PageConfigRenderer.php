<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin;

use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Aheadworks\Layerednav\Model\Template\FilterProvider;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Page\Config\RendererInterface;

/**
 * Class PageConfigRenderer
 * @package Aheadworks\Layerednav\Model\Plugin
 */
class PageConfigRenderer
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
     * @var FilterProvider
     */
    private $templateFilterProvider;

    /**
     * @var PageConfig
     */
    private $pageConfig;

    /**
     * @param Config $config
     * @param PageTypeResolver $pageTypeResolver
     * @param FilterProvider $templateFilterProvider
     * @param PageConfig $pageConfig
     */
    public function __construct(
        Config $config,
        PageTypeResolver $pageTypeResolver,
        FilterProvider $templateFilterProvider,
        PageConfig $pageConfig
    ) {
        $this->config = $config;
        $this->pageTypeResolver = $pageTypeResolver;
        $this->templateFilterProvider = $templateFilterProvider;
        $this->pageConfig = $pageConfig;
    }

    /**
     * @param RendererInterface $renderer
     * @param \Closure $proceed
     * @return string
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundRenderMetadata(RendererInterface $renderer, \Closure $proceed)
    {
        $metaData = $this->pageConfig->getMetadata();
        $result = $proceed();
        $pageMetaTitleTemplate = $this->config->getPageMetaTitleTemplate();
        if (!isset($metaData['title'])
            && $this->pageTypeResolver->getType() == PageTypeResolver::PAGE_TYPE_CATEGORY
            && !empty($pageMetaTitleTemplate)
        ) {
            $templateFilter = $this->templateFilterProvider->getFilter();
            $result .= sprintf(
                '<meta name="title" content="%s"/>' . "\n",
                $templateFilter->filter($pageMetaTitleTemplate)
            );
        }
        return $result;
    }
}
