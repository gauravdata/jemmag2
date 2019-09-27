<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request\Matcher;

use Aheadworks\Layerednav\App\Request\AttributeList;
use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\App\Request\Matcher\Base\PathMatcher;
use Aheadworks\Layerednav\App\Request\MatcherInterface;
use Aheadworks\Layerednav\Model\Config\Source\SeoFriendlyUrl;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;

/**
 * Class ValueInsteadOfId
 * @package Aheadworks\Layerednav\App\Request\Matcher
 */
class ValueInsteadOfId implements MatcherInterface
{
    /**
     * @var PathMatcher
     */
    private $pathMatcher;

    /**
     * @var AttributeList
     */
    private $attributeList;

    /**
     * @var ParamDataProvider
     */
    private $paramDataProvider;

    /**
     * @param PathMatcher $pathMatcher
     * @param AttributeList $attributeList
     * @param ParamDataProvider $paramDataProvider
     */
    public function __construct(
        PathMatcher $pathMatcher,
        AttributeList $attributeList,
        ParamDataProvider $paramDataProvider
    ) {
        $this->pathMatcher = $pathMatcher;
        $this->attributeList = $attributeList;
        $this->paramDataProvider = $paramDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function match(RequestInterface $request)
    {
        return $this->pathMatcher->match($request) && $this->matchParams($request);
    }

    /**
     * Match action by request params
     *
     * @param RequestInterface $request
     * @return bool
     */
    private function matchParams(RequestInterface $request)
    {
        $params = $request->getParams();
        $attributeCodes = $this->attributeList->getAttributeCodes();
        foreach ($params as $key => $value) {
            if (in_array($key, array_merge($attributeCodes, ['cat']))
                && (!is_string($value) || preg_match('/[^A-Za-z0-9_\-]/', $value))
            ) {
                return false;
            }
        }
        $decimalAttributeCodes = $this->attributeList->getAttributeCodes(AttributeList::LIST_TYPE_DECIMAL);
        foreach ($params as $key => $value) {
            if (in_array($key, $decimalAttributeCodes)
                && (!is_string($value) || !preg_match('/^[0-9]*[.,]*[0-9]*(-[0-9]*[.,]*[0-9]*)*$/', $value))
            ) {
                return false;
            }
        }
        $customFilterParams = $this->paramDataProvider->getCustomFilterParams();
        foreach ($params as $key => $value) {
            if (in_array($key, $customFilterParams) && is_numeric($value)) {
                return false;
            }
        }
        $filterParamKeys = array_merge(
            $attributeCodes,
            $decimalAttributeCodes,
            ['cat'],
            $customFilterParams
        );
        if (array_intersect($filterParamKeys, array_keys($params))) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return SeoFriendlyUrl::ATTRIBUTE_VALUE_INSTEAD_OF_ID;
    }
}
