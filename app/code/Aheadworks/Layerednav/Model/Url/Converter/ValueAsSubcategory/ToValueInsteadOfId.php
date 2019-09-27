<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url\Converter\ValueAsSubcategory;

use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\Model\Url\Converter\Base\Value\ToId as ToIdConverter;
use Aheadworks\Layerednav\Model\Url\Converter\Base\Id\ToValue as ToValueConverter;
use Aheadworks\Layerednav\Model\Url\ConverterInterface;

/**
 * Class ToValueInsteadOfId
 * @package Aheadworks\Layerednav\Model\Url\Converter\ValueAsSubcategory
 */
class ToValueInsteadOfId implements ConverterInterface
{
    /**
     * @var ToIdConverter
     */
    private $toIdConverter;

    /**
     * @var ToValueConverter
     */
    private $toValueConverter;

    /**
     * @var ParamDataProvider
     */
    private $paramDataProvider;

    /**
     * @param ToIdConverter $toIdConverter
     * @param ToValueConverter $toValueConverter
     * @param ParamDataProvider $paramDataProvider
     */
    public function __construct(
        ToIdConverter $toIdConverter,
        ToValueConverter $toValueConverter,
        ParamDataProvider $paramDataProvider
    ) {
        $this->toIdConverter = $toIdConverter;
        $this->toValueConverter = $toValueConverter;
        $this->paramDataProvider = $paramDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function convertFilterParams($params)
    {
        $params = $this->toIdConverter->convertFilterParams($params);
        $params = $this->toValueConverter->convertFilterParams($params);
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
        foreach ($customFilterParamsMap as $value => $key) {
            if (array_key_exists($value, $params)) {
                $params[$key] = $value;
                unset($params[$value]);
            }
        }
        return $params;
    }
}
