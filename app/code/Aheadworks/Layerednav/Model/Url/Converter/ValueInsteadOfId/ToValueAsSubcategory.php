<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Url\Converter\ValueInsteadOfId;

use Aheadworks\Layerednav\App\Request\ParamDataProvider;
use Aheadworks\Layerednav\Model\Url\Converter\Base\Value\ToId as ToIdConverter;
use Aheadworks\Layerednav\Model\Url\Converter\Base\Id\ToValue as ToValueConverter;
use Aheadworks\Layerednav\Model\Url\ConverterInterface;

/**
 * Class ToValueAsSubcategory
 * @package Aheadworks\Layerednav\Model\Url\Converter\ValueInsteadOfId
 */
class ToValueAsSubcategory implements ConverterInterface
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
        foreach ($this->paramDataProvider->getCustomFilterParamSeoFriendlyValues() as $key => $value) {
            if (array_key_exists($key, $params)) {
                $params[$value] = 1;
                unset($params[$key]);
            }
        }
        return $params;
    }
}
