<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Filter\Swatch;

use Aheadworks\Layerednav\Model\Filter\Swatch\Repository as SwatchRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Finder
 *
 * @package Aheadworks\Layerednav\Model\Filter\Swatch
 */
class Finder
{
    /**
     * @var SwatchRepository
     */
    private $swatchRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param SwatchRepository $swatchRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        SwatchRepository $swatchRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->swatchRepository = $swatchRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Retrieve array of swatches for specific AW LN filter
     *
     * @param int $filterId
     * @return SwatchInterface[]
     */
    public function getByFilterId($filterId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(SwatchInterface::FILTER_ID, $filterId);

        try {
            $swatches = $this->swatchRepository->getList($this->searchCriteriaBuilder->create());
        } catch (NoSuchEntityException $exception) {
            $swatches = [];
        }

        return $swatches;
    }

    /**
     * Retrieve swatch item for specific option id
     *
     * @param int $optionId
     * @return SwatchInterface|null
     */
    public function getByOptionId($optionId)
    {
        $swatchItem = null;
        $this->searchCriteriaBuilder
            ->addFilter(SwatchInterface::OPTION_ID, $optionId);

        try {
            $swatches = $this->swatchRepository->getList($this->searchCriteriaBuilder->create());
            $swatchItem = reset($swatches);
        } catch (NoSuchEntityException $exception) {
            $swatchItem = null;
        }

        return $swatchItem;
    }
}
