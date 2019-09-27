<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Factory;

use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\DisplayStateResolver;
use Aheadworks\Layerednav\Model\Filter\ModeResolver as FilterModeResolver;
use \Aheadworks\Layerednav\Model\Layer\Filter as LayerFilter;

/**
 * Class CustomFactory
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Factory
 */
class Custom implements DataProviderInterface
{
    /**
     * @var ImageViewDataResolver
     */
    private $imageViewDataResolver;

    /**
     * @var DisplayStateResolver
     */
    private $displayStateResolver;

    /**
     * @var FilterModeResolver
     */
    private $filterModeResolver;

    /**
     * @param ImageViewDataResolver $imageViewDataResolver
     * @param DisplayStateResolver $displayStateResolver
     * @param FilterModeResolver $filterModeResolver
     */
    public function __construct(
        ImageViewDataResolver $imageViewDataResolver,
        DisplayStateResolver $displayStateResolver,
        FilterModeResolver $filterModeResolver
    ) {
        $this->imageViewDataResolver = $imageViewDataResolver;
        $this->displayStateResolver = $displayStateResolver;
        $this->filterModeResolver = $filterModeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(FilterInterface $filterEntity, $attribute = null)
    {
        return [
            LayerFilter::CODE => $filterEntity->getCode(),
            LayerFilter::TITLE => $filterEntity->getStorefrontTitle(),
            LayerFilter::TYPE => $filterEntity->getType(),
            LayerFilter::IMAGE => $this->imageViewDataResolver->getImageView($filterEntity),
            LayerFilter::ATTRIBUTE => $attribute,
            LayerFilter::ADDITIONAL_DATA => [
                FilterInterface::STOREFRONT_DISPLAY_STATE =>
                    $this->displayStateResolver->getStorefrontDisplayState($filterEntity),
                ModeInterface::STOREFRONT_FILTER_MODE =>
                    $this->filterModeResolver->getStorefrontFilterMode($filterEntity),
                FilterInterface::IMAGE_STOREFRONT_TITLE => $filterEntity->getImageStorefrontTitle(),
                FilterInterface::SWATCHES_VIEW_MODE => $filterEntity->getSwatchesViewMode(),
            ]
        ];
    }
}
