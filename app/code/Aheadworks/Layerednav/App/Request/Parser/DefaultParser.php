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
use Magento\Framework\App\RequestInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;

/**
 * Class DefaultParser
 * @package Aheadworks\Layerednav\App\Request\Parser
 */
class DefaultParser implements ParserInterface
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
     * @param AttributeList $attributeList
     * @param CategoryList $categoryList
     * @param ParamDataProvider $paramDataProvider
     */
    public function __construct(
        AttributeList $attributeList,
        CategoryList $categoryList,
        ParamDataProvider $paramDataProvider
    ) {
        $this->attributeList = $attributeList;
        $this->categoryList = $categoryList;
        $this->paramDataProvider = $paramDataProvider;
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
            if (array_key_exists($attributeCode, $params)) {
                $filterParams[$attributeCode] = $params[$attributeCode];
            }
        }
        $this->collectFilterParams(
            $filterParams,
            $params,
            'cat',
            $this->getCategoryIds()
        );
        foreach ($this->paramDataProvider->getCustomFilterParams() as $paramKey) {
            if (array_key_exists($paramKey, $params)) {
                $filterParams[$paramKey] = 1;
            }
        }
        return ['filterParams' => $filterParams];
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
            $paramValues = explode(',', $source[$key]);
            $filterParamValues = [];
            foreach ($paramValues as $value) {
                if (in_array($value, $candidates)) {
                    $filterParamValues[] = $value;
                }
            }
            if (!empty($filterParamValues)) {
                $destination[$key] = implode(',', $filterParamValues);
            }
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
            $result[] = isset($option[AttributeOptionInterface::VALUE])
                ? $option[AttributeOptionInterface::VALUE]
                : '';
        }
        return $result;
    }

    /**
     * Get category Ids
     *
     * @return array
     */
    private function getCategoryIds()
    {
        $result = [];
        foreach ($this->categoryList->getCategoriesKeyedByUrlKey() as $category) {
            $result[] = $category['id'];
        }
        return $result;
    }
}
