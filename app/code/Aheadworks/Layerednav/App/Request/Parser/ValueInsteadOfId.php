<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\App\Request\Parser;

use Aheadworks\Layerednav\App\Request\AttributeList;
use Aheadworks\Layerednav\App\Request\CategoryList;
use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\App\Request\ParserInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\FilterManager;

/**
 * Class ValueInsteadOfId
 * @package Aheadworks\Layerednav\App\Request\Parser
 */
class ValueInsteadOfId implements ParserInterface
{
    /**
     * @var AttributeList
     */
    private $attributeList;

    /**
     * @var CategoryList
     */
    private $categoryList;

    /**
     * @var ParamDataProvider
     */
    private $paramDataProvider;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * @param AttributeList $attributeList
     * @param CategoryList $categoryList
     * @param ParamDataProvider $paramDataProvider
     * @param FilterManager $filterManager
     */
    public function __construct(
        AttributeList $attributeList,
        CategoryList $categoryList,
        ParamDataProvider $paramDataProvider,
        FilterManager $filterManager
    ) {
        $this->attributeList = $attributeList;
        $this->categoryList = $categoryList;
        $this->paramDataProvider = $paramDataProvider;
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(RequestInterface $request)
    {
        $filterParams = [];
        $params = $request->getParams();
        $attributes = $this->attributeList->getAttributesKeyedByCode();
        /** @var array $attribute */
        foreach ($attributes as $attributeCode => $attribute) {
            $this->collectFilterParams(
                $filterParams,
                $params,
                $attributeCode,
                $this->getAttributeParamValues($attribute)
            );
        }
        $decimalAttributeCodes = $this->attributeList->getAttributeCodes(AttributeList::LIST_TYPE_DECIMAL);
        foreach ($decimalAttributeCodes as $attributeCode) {
            $this->collectDecimalFilterParams($filterParams, $params, $attributeCode);
        }

        $this->collectCategoryFilterParams($filterParams, $params);

        foreach ($this->paramDataProvider->getCustomFilterParams() as $paramKey) {
            if (array_key_exists($paramKey, $params)) {
                $filterParams[$paramKey] = 1;
            }
        }
        return [
            'filterParams' => $filterParams,
        ];
    }

    /**
     * Collect filter params
     *
     * @param array $destination
     * @param array $source
     * @param string $key
     * @param array $candidates
     * @return void
     */
    private function collectFilterParams(&$destination, $source, $key, $candidates)
    {
        if (array_key_exists($key, $source)) {
            $paramValues = preg_split('/(?<!-{1})-{1}(?!-{1})/', $source[$key]);
            $filterParamValues = [];
            foreach ($paramValues as $value) {
                $preparedValue = preg_replace_callback(
                    '/-{1,}/',
                    function (array $matches) {
                        return substr($matches[0], 0, strlen($matches[0]) - 1);
                    },
                    urldecode($value)
                );
                if (in_array($preparedValue, $candidates)) {
                    $filterParamValues[] = $preparedValue;
                }
            }
            if (!empty($filterParamValues)) {
                $destination[$key] = implode(',', $filterParamValues);
            }
        }
    }

    /**
     * Collect decimal filter params
     *
     * @param array $destination
     * @param array $source
     * @param string $key
     * @return void
     */
    private function collectDecimalFilterParams(&$destination, $source, $key)
    {
        if (array_key_exists($key, $source)) {
            $paramValues = preg_split('/-{2}(?!-{1})/', $source[$key]);
            $destination[$key] = implode(',', $paramValues);
        }
    }

    /**
     * Get attribute request param values
     *
     * @param array $attribute
     * @return array
     */
    private function getAttributeParamValues($attribute)
    {
        $result = [];
        foreach ($attribute['options'] as $option) {
            $optionLabel =  isset($option[AttributeOptionInterface::LABEL])
                ? $option[AttributeOptionInterface::LABEL]
                : '';
            $result[] = $this->filterManager->translitUrl(urlencode($optionLabel));
        }
        return $result;
    }

    /**
     * Collect category filter params
     *
     * @param array $destination
     * @param array $params
     * @return void
     */
    private function collectCategoryFilterParams(&$destination, $params)
    {
        $this->collectFilterParams(
            $destination,
            $params,
            'cat',
            $this->categoryList->getCategoryUrlKeys()
        );
        if (isset($destination['cat']) && isset($params['id'])) {
            $destination['parent_cat_id'] = $params['id'];
        }
    }
}
