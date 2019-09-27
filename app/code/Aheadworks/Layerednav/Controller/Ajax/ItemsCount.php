<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Controller\Ajax;

use Aheadworks\Layerednav\Model\Layer\Applier;
use Aheadworks\Layerednav\Model\Config;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Layer\FilterListResolver;
use Aheadworks\Layerednav\Model\PageTypeResolver;
use Aheadworks\Layerednav\Model\Url\ConverterPool;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Search\Model\QueryFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ItemsCount
 * @package Aheadworks\Layerednav\Controller\Ajax
 */
class ItemsCount extends Action
{
    /**
     * @var Resolver
     */
    private $layerResolver;

    /**
     * @var PageTypeResolver
     */
    private $pageTypeResolver;

    /**
     * @var FilterListResolver
     */
    private $filterListResolver;

    /**
     * @var Applier
     */
    private $applier;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ConverterPool
     */
    private $urlConverterPool;

    /**
     * @param Context $context
     * @param Resolver $layerResolver
     * @param PageTypeResolver $pageTypeResolver
     * @param FilterListResolver $filterListResolver
     * @param Applier $applier
     * @param Config $config
     * @param ConverterPool $urlConverterPool
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        PageTypeResolver $pageTypeResolver,
        FilterListResolver $filterListResolver,
        Applier $applier,
        Config $config,
        ConverterPool $urlConverterPool
    ) {
        parent::__construct($context);
        $this->layerResolver = $layerResolver;
        $this->pageTypeResolver = $pageTypeResolver;
        $this->filterListResolver = $filterListResolver;
        $this->applier = $applier;
        $this->config = $config;
        $this->urlConverterPool = $urlConverterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        $requestParams = $this->getRequest()->getParams();
        if (isset($requestParams['filterValue'])) {
            $requestParams = array_merge($requestParams, $this->prepareFilterValue($requestParams['filterValue']));
        }
        $pageType = $this->getRequest()->getParam('pageType');
        if ($this->pageTypeResolver->isSearchPage($pageType)) {
            $requestParams[QueryFactory::QUERY_VAR_NAME] = $this->getRequest()->getParam('searchQueryText');
        }
        $this->getRequest()->setParams($requestParams);

        try {
            $this->filterListResolver->create($this->getRequest()->getParam('pageType'));

            $layer = $this->getLayer();
            $this->applier->applyFilters($layer);

            $itemsCount = $layer->getProductCollection()->getSize();
            return $resultJson->setData(
                [
                    'success' => true,
                    'sequence' => $this->getRequest()->getParam('sequence'),
                    'itemsCount' => $itemsCount,
                    'itemsContent' => __($itemsCount == 1 ? __('%1 item') : __('%1 items'), $itemsCount)
                ]
            );
        } catch (\Exception $e) {
            return $resultJson->setData(['success' => false]);
        }
    }

    /**
     * Prepare filter value
     *
     * @param array $filterValue
     * @return array
     */
    private function prepareFilterValue($filterValue)
    {
        $filterParams = [];
        foreach ($filterValue as $value) {
            $filterParams[$value['key']][] = $value['value'];
        }
        foreach ($filterParams as $key => $param) {
            $filterParams[$key] = implode(',', $param);
        }
        $seoFriendlyOption = $this->config->getSeoFriendlyUrlOption();
        if ($seoFriendlyOption != SeoFriendlyUrl::DEFAULT_OPTION
            && $this->getRequest()->getParam('pageType') !=  PageTypeResolver::PAGE_TYPE_CATALOG_SEARCH
        ) {
            return $this->urlConverterPool
                ->getConverter($seoFriendlyOption, SeoFriendlyUrl::DEFAULT_OPTION)
                ->convertFilterParams($filterParams);
        }
        return $filterParams;
    }

    /**
     * Get layer object
     *
     * @return Layer
     * @throws LocalizedException
     */
    private function getLayer()
    {
        $pageType = $this->getRequest()->getParam('pageType');
        $this->layerResolver->create($this->pageTypeResolver->getLayerType($pageType));
        $layer = $this->layerResolver->get();
        $layer->setCurrentCategory($this->getRequest()->getParam('categoryId'));
        return $layer;
    }
}
