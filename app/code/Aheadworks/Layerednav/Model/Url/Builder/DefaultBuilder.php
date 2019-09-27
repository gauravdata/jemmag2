<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url\Builder;

use Aheadworks\Layerednav\App\Request\ParserPool;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Aheadworks\Layerednav\Model\Url\BuilderInterface;
use Aheadworks\Layerednav\Model\Url\ConverterPool;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;

/**
 * Class DefaultBuilder
 * @package Aheadworks\Layerednav\Model\Url\Builder
 */
class DefaultBuilder implements BuilderInterface
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
     * @param ParserPool $parserPool
     * @param RequestInterface $request
     * @param ConverterPool $converterPool
     * @param UrlInterface $url
     */
    public function __construct(
        ParserPool $parserPool,
        RequestInterface $request,
        ConverterPool $converterPool,
        UrlInterface $url
    ) {
        $this->parserPool = $parserPool;
        $this->request = $request;
        $this->converterPool = $converterPool;
        $this->url = $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUrl($fromType)
    {
        $this->request->clearParams();
        $converter = $this->converterPool->getConverter($fromType, SeoFriendlyUrl::DEFAULT_OPTION);

        $parts = $this->parserPool->getParser($fromType)->parse($this->request);
        list($pathParams, $filterParams) = [
            isset($parts['pathParams']) ? $parts['pathParams'] : null,
            $parts['filterParams']
        ];

        $url = $pathParams
            ? implode('/', $pathParams)
            : trim($this->request->getPathInfo(), '/');
        $params = array_merge(
            $this->request->getParams(),
            $converter->convertFilterParams($filterParams)
        );
        return $this->url->addQueryParams($params)->getDirectUrl($url);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCanonicalUrl()
    {
        $parts = $this->parserPool->getParser(SeoFriendlyUrl::DEFAULT_OPTION)
            ->parse($this->request);

        return $this->url
            ->addQueryParams($parts['filterParams'])
            ->getDirectUrl(trim($this->request->getPathInfo(), '/'));
    }
}
