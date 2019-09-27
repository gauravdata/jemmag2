<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url\Converter\DefaultConverter;

use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\Model\Url\Converter\Base\Id\ToValue as ToValueConverter;
use Aheadworks\Layerednav\Model\Url\ConverterInterface;

/**
 * Class ToValueAsSubcategory
 * @package Aheadworks\Layerednav\Model\Url\Converter\DefaultConverter
 */
class ToValueAsSubcategory implements ConverterInterface
{
    /**
     * @var ToValueConverter
     */
    private $baseConverter;

    /**
     * @var ParamDataProvider
     */
    private $paramDataProvider;

    /**
     * @param ToValueConverter $baseConverter
     * @param ParamDataProvider $paramDataProvider
     */
    public function __construct(
        ToValueConverter $baseConverter,
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
        foreach ($this->paramDataProvider->getCustomFilterParamSeoFriendlyValues() as $key => $value) {
            if (array_key_exists($key, $params)) {
                $params[$value] = 1;
                unset($params[$key]);
            }
        }
        return $params;
    }
}
