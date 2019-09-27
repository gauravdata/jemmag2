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
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\FilterManager;

/**
 * Class ValueAsSubcategory
 * @package Aheadworks\Layerednav\App\Request\Parser
 */
class ValueAsSubcategory implements ParserInterface
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
        $this->filterManager = $filterManager;
        $this->paramDataProvider = $paramDataProvider;
    }

    /**
     * Parse request
     *
     * @param RequestInterface|Http $request
     * @return array
     */
    public function parse(RequestInterface $request)
    {
        $path = trim($request->getPathInfo(), '/');
        $parts = explode('?', $path);
        $params = explode('/', $parts[0]);
        $requestParams = $request->getParams();

        $keyedParams = [];
        foreach ($params as $key => $param) {
            $keyedParams['param-' . $key] = $param;
        }

        $filterParamsCandidates = [];
        $attributes = $this->attributeList->getAttributesKeyedByCode();
        $attributeCodes = array_keys($attributes);
        $this->collectFilterParamsCandidates(
            $filterParamsCandidates,
            $keyedParams,
            $attributeCodes
        );

        $this->collectFilterParamsCandidates(
            $filterParamsCandidates,
            $keyedParams,
            ['category']
        );
        $decimalAttributeCodes = $this->attributeList->getAttributeCodes(AttributeList::LIST_TYPE_DECIMAL);
        $this->collectFilterParamsCandidates(
            $filterParamsCandidates,
            $keyedParams,
            $decimalAttributeCodes
        );

        $filterParams = ['keys' => [], 'params' => []];
        $this->collectFilterParams(
            $filterParams,
            $filterParamsCandidates,
            $attributeCodes,
            $this->getAttributeParamValuesKeyedByAttrCode($attributes)
        );

        $this->collectCategoryFilterParams($filterParams, $filterParamsCandidates, $requestParams);

        $this->collectFilterParamsDecimal(
            $filterParams,
            $filterParamsCandidates,
            $decimalAttributeCodes
        );

        $keyedParams = array_filter(
            $keyedParams,
            function ($key) use ($filterParams) {
                return !in_array($key, $filterParams['keys']);
            },
            ARRAY_FILTER_USE_KEY
        );

        return [
            'filterParams' => $filterParams['params'],
            'pathParams' => array_values($keyedParams)
        ];
    }

    /**
     * Collect filter params candidates
     *
     * @param array $destination
     * @param array $sourceKeyed
     * @param array $keyCandidates
     * @return void
     */
    private function collectFilterParamsCandidates(&$destination, $sourceKeyed, $keyCandidates)
    {
        $keys = array_keys($sourceKeyed);
        $customFilterParams = $this->paramDataProvider->getCustomFilterParamSeoFriendlyValues();
        if (is_array($keys)) {
            while (count($keys)) {
                $key = array_pop($keys);
                $param = $sourceKeyed[$key];
                if (array_key_exists($param, $destination)) {
                    continue;
                }
                if (!in_array($param, $customFilterParams)) {
                    $keyValueCandidates = preg_split('/(?<!-{1})-{1}(?!-{1})/', $param);
                    if (is_array($keyValueCandidates)
                        && count($keyValueCandidates) > 1
                        && in_array(
                            $keyValueCandidates[0],
                            $keyCandidates
                        )
                    ) {
                        $destination[$param] = $key;
                    }
                } else {
                    $destination[$param] = $key;
                }
            }
        }
    }

    /**
     * Collect filter params
     *
     * @param array $destination
     * @param array $paramCandidates
     * @param array $keyCandidates
     * @param array $valueCandidatesKeyed
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function collectFilterParams(
        &$destination,
        $paramCandidates,
        $keyCandidates,
        $valueCandidatesKeyed
    ) {
        $customFilterParams = $this->paramDataProvider->getCustomFilterParamSeoFriendlyValues();
        foreach ($paramCandidates as $candidate => $candidateKey) {
            $collected = false;

            if (!in_array($candidate, $customFilterParams)) {
                $keyValueCandidates = preg_split('/(?<!-{1})-{1}(?!-{1})/', $candidate);
                if (is_array($keyValueCandidates)
                    && count($keyValueCandidates) > 1
                ) {
                    $key = array_shift($keyValueCandidates);
                    if (in_array($key, $keyCandidates)) {
                        $valuesCandidatesPrepared = [];
                        foreach ($keyValueCandidates as $valueCandidate) {
                            $valuesCandidatesPrepared[] = preg_replace_callback(
                                '/-{1,}/',
                                function (array $matches) {
                                    return substr($matches[0], 0, strlen($matches[0]) - 1);
                                },
                                urldecode($valueCandidate)
                            );
                        }

                        $filterParamValues = [];
                        foreach ($valuesCandidatesPrepared as $valueCandidate) {
                            if (in_array($valueCandidate, $valueCandidatesKeyed[$key])) {
                                $filterParamValues[] = $valueCandidate;
                            }
                        }

                        if ($key == 'category') {
                            $key = 'cat';
                        }
                        if (!isset($destination['params'][$key])) {
                            $destination['params'][$key] = implode(',', $filterParamValues);
                            $collected = true;
                        }
                    }
                }
            } else {
                if (!isset($destination['params'][$candidate])) {
                    $destination['params'][$candidate] = 1;
                    $collected = true;
                }
            }

            if ($collected) {
                $destination['keys'][] = $candidateKey;
            }
        }
    }

    /**
     * Collect decimal filter params
     *
     * @param array $destination
     * @param array $paramCandidates
     * @param array $keyCandidates
     * @return void
     */
    private function collectFilterParamsDecimal(&$destination, $paramCandidates, $keyCandidates)
    {
        $customFilterParams = $this->paramDataProvider->getCustomFilterParamSeoFriendlyValues();
        foreach ($paramCandidates as $candidate => $candidateKey) {
            $collected = false;

            if (!in_array($candidate, $customFilterParams)) {
                $keyValueCandidates = preg_split(
                    '/(?<=' . implode('|', $keyCandidates) . ')-{1}|-{2}(?!-{1})/',
                    $candidate
                );
                if (is_array($keyValueCandidates)
                    && count($keyValueCandidates) > 1
                ) {
                    $key = array_shift($keyValueCandidates);
                    if (in_array($key, $keyCandidates)
                        && !isset($destination['params'][$key])
                    ) {
                        $destination['params'][$key] = implode(',', $keyValueCandidates);
                        $collected = true;
                    }
                }
            } else {
                if (!isset($destination['params'][$candidate])) {
                    $destination['params'][$candidate] = 1;
                    $collected = true;
                }
            }

            if ($collected) {
                $destination['keys'][] = $candidateKey;
            }
        }
    }

    /**
     * Get attribute request param values by attribute code
     *
     * @param ProductAttributeInterface[] $attributes
     * @return array
     */
    private function getAttributeParamValuesKeyedByAttrCode($attributes)
    {
        $result = [];
        foreach ($attributes as $attrCode => $attribute) {
            $values = [];
            foreach ($attribute['options'] as $option) {
                $optionLabel =  isset($option[AttributeOptionInterface::LABEL])
                    ? $option[AttributeOptionInterface::LABEL]
                    : '';
                $values[] = $this->filterManager->translitUrl(urlencode($optionLabel));
            }
            $result[$attrCode] = $values;
        }
        return $result;
    }

    /**
     * Collect category filter params
     *
     * @param array $destination
     * @param array $candidates
     * @param array $requestParams
     * @return void
     */
    private function collectCategoryFilterParams(&$destination, $candidates, $requestParams)
    {
        $categoryUrlKeys = $this->categoryList->getCategoryUrlKeys();
        $this->collectFilterParams(
            $destination,
            $candidates,
            ['category'],
            ['category' => $categoryUrlKeys]
        );
        if (isset($destination['params']['cat']) && isset($requestParams['id'])) {
            $destination['params']['parent_cat_id'] = $requestParams['id'];
        }
    }
}
