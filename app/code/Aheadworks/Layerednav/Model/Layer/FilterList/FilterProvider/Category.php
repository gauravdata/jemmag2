<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\FilterList\FilterProvider;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Model\Layer\FilterList\FilterProviderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;

/**
 * Class Category
 * @package Aheadworks\Layerednav\Model\Layer\FilterList\FilterProvider
 */
class Category implements FilterProviderInterface
{
    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @param FilterRepositoryInterface $filterRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        FilterRepositoryInterface $filterRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->filterRepository = $filterRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterDataObjects()
    {
        $this->sortOrderBuilder
            ->setField(FilterInterface::POSITION)
            ->setAscendingDirection();
        $this->searchCriteriaBuilder
            ->addFilter(FilterInterface::IS_FILTERABLE, 0, 'gt')
            ->addSortOrder($this->sortOrderBuilder->create());

        return $this->filterRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }
}
