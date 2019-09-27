<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Factory;

use Aheadworks\Layerednav\Api\Data\Filter\ModeInterface;
use Aheadworks\Layerednav\Api\Data\FilterCategoryInterface;
use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Filter;
use Aheadworks\Layerednav\Model\Filter\DisplayStateResolver;
use Aheadworks\Layerednav\Model\Filter\ModeResolver as FilterModeResolver;
use Aheadworks\Layerednav\Model\Layer\Filter as LayerFilter;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Category
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Factory
 */
class Category implements DataProviderInterface
{
    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

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
     * @param FilterRepositoryInterface $filterRepository
     * @param ImageViewDataResolver $imageViewDataResolver
     * @param DisplayStateResolver $displayStateResolver
     * @param FilterModeResolver $filterModeResolver
     */
    public function __construct(
        FilterRepositoryInterface $filterRepository,
        ImageViewDataResolver $imageViewDataResolver,
        DisplayStateResolver $displayStateResolver,
        FilterModeResolver $filterModeResolver
    ) {
        $this->filterRepository = $filterRepository;
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
            LayerFilter::ADDITIONAL_DATA => $this->getAdditionalData($filterEntity)
        ];
    }

    /**
     * Get additional data
     *
     * @param FilterInterface $filterEntity
     * @return array
     * @throws NoSuchEntityException
     */
    private function getAdditionalData(FilterInterface $filterEntity)
    {
        $additionalData = [
            FilterInterface::STOREFRONT_DISPLAY_STATE =>
                $this->displayStateResolver->getStorefrontDisplayState($filterEntity),
            ModeInterface::STOREFRONT_FILTER_MODE =>
                $this->filterModeResolver->getStorefrontFilterMode($filterEntity),
            FilterInterface::IMAGE_STOREFRONT_TITLE => $filterEntity->getImageStorefrontTitle(),
            FilterInterface::SWATCHES_VIEW_MODE => $filterEntity->getSwatchesViewMode(),
        ];
        /** @var FilterInterface|Filter $categoryFilterObject */
        $categoryFilterObject = $this->filterRepository->get($filterEntity->getId());
        /** @var FilterCategoryInterface $categoryFilterData */
        $categoryFilterData = $categoryFilterObject->getCategoryFilterData();
        if ($categoryFilterData) {
            $additionalData[FilterCategoryInterface::STOREFRONT_LIST_STYLE] =
                $categoryFilterData->getStorefrontListStyle();
        }

        return $additionalData;
    }
}
