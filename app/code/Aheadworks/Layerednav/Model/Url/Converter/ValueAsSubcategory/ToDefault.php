<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url\Converter\ValueAsSubcategory;

use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\Model\Url\ConverterInterface;
use Aheadworks\Layerednav\Model\Url\Converter\Base\Value\ToId as ToIdConverter;

/**
 * Class ToDefault
 * @package Aheadworks\Layerednav\Model\Url\Converter\ValueAsSubcategory
 */
class ToDefault implements ConverterInterface
{
    /**
     * @var ToIdConverter
     */
    private $baseConverter;

    /**
     * @var ParamDataProvider
     */
    private $paramDataProvider;

    /**
     * @param ToIdConverter $baseConverter
     * @param ParamDataProvider $paramDataProvider
     */
    public function __construct(
        ToIdConverter $baseConverter,
        ParamDataProvider $paramDataProvider
    ) {
        $this->baseConverter = $baseConverter;
        $this->paramDataProvider = $paramDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function convertFilterParams($params)
    {
        $params = $this->baseConverter->convertFilterParams($params);
        $params = $this->convertCustomFilterParams($params);
        return $params;
    }

    /**
     * Convert custom filters params
     *
     * @param array $params
     * @return array
     */
    private function convertCustomFilterParams($params)
    {
        $customFilterParamsMap = array_flip(
            $this->paramDataProvider->getCustomFilterParamSeoFriendlyValues()
        );
        foreach ($customFilterParamsMap as $requestParam => $filterParam) {
            if (array_key_exists($requestParam, $params)) {
                $params[$filterParam] = $params[$requestParam];
                unset($params[$requestParam]);
            } elseif (array_key_exists($filterParam, $params)) {
                $params[$filterParam] = 1;
            }
        }
        return $params;
    }
}
