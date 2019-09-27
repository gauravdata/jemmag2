<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request\Processor;

use Aheadworks\Layerednav\App\Request\Parser\ValueAsSubcategory as Parser;
use Aheadworks\Layerednav\App\Request\ProcessorInterface;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Url\ConverterPool;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class ValueAsSubcategory
 * @package Aheadworks\Layerednav\App\Request\Processor
 */
class ValueAsSubcategory implements ProcessorInterface
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @var ConverterPool
     */
    private $converterPool;

    /**
     * @param Parser $parser
     * @param StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param ConverterPool $converterPool
     */
    public function __construct(
        Parser $parser,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        ConverterPool $converterPool
    ) {
        $this->parser = $parser;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->converterPool = $converterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function process(RequestInterface $request)
    {
        $parts = $this->parser->parse($request);
        $rewrite = $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => implode('/', $parts['pathParams']),
            UrlRewrite::STORE_ID => $this->storeManager->getStore()->getId(),
        ]);
        if ($rewrite !== null) {
            $request->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $rewrite->getRequestPath());
            $request->setPathInfo('/' . $rewrite->getTargetPath());
        }

        $converter = $this->converterPool
            ->getConverter(
                SeoFriendlyUrl::ATTRIBUTE_VALUE_AS_SUBCATEGORY,
                SeoFriendlyUrl::DEFAULT_OPTION
            );
        $request->setParams(
            array_merge(
                $request->getParams(),
                $converter->convertFilterParams($parts['filterParams'])
            )
        );

        return $request;
    }
}
