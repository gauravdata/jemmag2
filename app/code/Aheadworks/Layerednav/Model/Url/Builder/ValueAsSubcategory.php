<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url\Builder;

use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\App\Request\ParserPool;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Url\BuilderInterface;
use Aheadworks\Layerednav\Model\Url\ConverterPool;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

/**
 * Class ValueAsSubcategory
 * @package Aheadworks\Layerednav\Model\Url\Builder
 */
class ValueAsSubcategory implements BuilderInterface
{
    /**
     * @var ParserPool
     */
    private $parserPool;

    /**
     * @var RequestInterface|Http
     */
    private $request;

    /**
     * @var ConverterPool
     */
    private $converterPool;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ParamDataProvider
     */
    private $paramDataProvider;

    /**
     * @param ParserPool $parserPool
     * @param RequestInterface $request
     * @param ConverterPool $converterPool
     * @param UrlInterface $url
     * @param ParamDataProvider $paramDataProvider
     */
    public function __construct(
        ParserPool $parserPool,
        RequestInterface $request,
        ConverterPool $converterPool,
        UrlInterface $url,
        ParamDataProvider $paramDataProvider
    ) {
        $this->parserPool = $parserPool;
        $this->request = $request;
        $this->converterPool = $converterPool;
        $this->url = $url;
        $this->paramDataProvider = $paramDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUrl($fromType)
    {
        $this->request->clearParams();
        $converter = $this->converterPool->getConverter(
            $fromType,
            SeoFriendlyUrl::ATTRIBUTE_VALUE_AS_SUBCATEGORY
        );
        $parts = $this->parserPool->getParser($fromType)->parse($this->request);
        $filterParams = $converter->convertFilterParams($parts['filterParams']);

        $customFilterParams = $this->paramDataProvider->getCustomFilterParams();
        $params = $this->request->getParams();
        foreach (array_keys($params) as $paramName) {
            if (isset($filterParams[$paramName])) {
                unset($params[$paramName]);
                if ($paramName == 'cat') {
                    $filterParams['category'] = $filterParams['cat'];
                    unset($filterParams['cat']);
                }
            }
            if (in_array($paramName, $customFilterParams)) {
                unset($params[$paramName]);
            }
        }

        $customFilterParamValues = $this->paramDataProvider->getCustomFilterParamSeoFriendlyValues();
        $url = trim($this->request->getPathInfo(), '/');
        foreach ($filterParams as $paramName => $paramValue) {
            if (!in_array($paramName, $customFilterParamValues)) {
                $url .= '/' . $paramName . '-' . $paramValue;
            } else {
                $url .= '/' . $paramName;
            }
        }

        return $this->url->addQueryParams($params)->getDirectUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCanonicalUrl()
    {
        return $this->url->getDirectUrl(trim($this->request->getOriginalPathInfo(), '/'));
    }
}
