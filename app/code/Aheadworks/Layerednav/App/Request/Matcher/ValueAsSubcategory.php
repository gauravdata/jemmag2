<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request\Matcher;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\App\Request\AttributeList;
use Aheadworks\Layerednav\App\Request\Matcher\Base\PathMatcher;
use Aheadworks\Layerednav\App\Request\MatcherInterface;
use Aheadworks\Layerednav\App\Request\Parser\ValueAsSubcategory as Parser;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

/**
 * Class ValueAsSubcategory
 * @package Aheadworks\Layerednav\App\Request\Matcher
 */
class ValueAsSubcategory implements MatcherInterface
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
     * @var PathMatcher
     */
    private $pathMatcher;

    /**
     * @var AttributeList
     */
    private $attributeList;

    /**
     * @var array
     */
    private $customFilterParams = [
        FilterInterface::STOCK_FILTER,
        FilterInterface::SALES_FILTER,
        FilterInterface::NEW_FILTER
    ];

    /**
     * @param Parser $parser
     * @param StoreManagerInterface $storeManager
     * @param UrlFinderInterface $urlFinder
     * @param PathMatcher $pathMatcher
     * @param AttributeList $attributeList
     */
    public function __construct(
        Parser $parser,
        StoreManagerInterface $storeManager,
        UrlFinderInterface $urlFinder,
        PathMatcher $pathMatcher,
        AttributeList $attributeList
    ) {
        $this->parser = $parser;
        $this->storeManager = $storeManager;
        $this->urlFinder = $urlFinder;
        $this->pathMatcher = $pathMatcher;
        $this->attributeList = $attributeList;
    }

    /**
     * {@inheritdoc}
     */
    public function match(RequestInterface $request)
    {
        $params = $this->parser->parse($request);
        $rewrite = $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => implode('/', $params['pathParams']),
            UrlRewrite::STORE_ID => $this->storeManager->getStore()->getId(),
        ]);
        if ($rewrite !== null) {
            $request->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $rewrite->getRequestPath());
            $request->setPathInfo('/' . $rewrite->getTargetPath());
        }
        $result = $this->pathMatcher->match($request) && $this->matchParams($params['filterParams']);
        if ($result) {
            $request->setPathInfo($request->getOriginalPathInfo());
        }
        return $result;
    }

    /**
     * Match action by request params
     *
     * @param array $params
     * @return bool
     */
    private function matchParams($params)
    {
        if (!is_array($params) || !count($params)) {
            return false;
        }
        $attributeCodes = $this->attributeList->getAttributeCodes();
        foreach ($params as $key => $value) {
            if (in_array($key, array_merge($attributeCodes, ['cat']))
                && preg_match('/[^A-Za-z0-9_\-,]/', $value)
                && !in_array($key, $this->customFilterParams)
            ) {
                return false;
            }
        }
        $decimalAttributeCodes = $this->attributeList->getAttributeCodes(AttributeList::LIST_TYPE_DECIMAL);
        foreach ($params as $key => $value) {
            if (in_array($key, $decimalAttributeCodes)
                && !preg_match('/^[0-9]*[.,]*[0-9]*(-[0-9]*[.,]*[0-9]*)*$/', $value)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return SeoFriendlyUrl::ATTRIBUTE_VALUE_AS_SUBCATEGORY;
    }
}
