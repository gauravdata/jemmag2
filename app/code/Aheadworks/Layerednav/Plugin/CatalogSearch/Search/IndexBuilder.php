<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin\CatalogSearch\Search;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier;
use Aheadworks\Layerednav\Model\Search\Request\FilterChecker;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\Context;
use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\ContextBuilder;
use Magento\CatalogSearch\Model\Search\IndexBuilder as SearchIndexBuilder;
use Magento\CatalogSearch\Model\Search\FiltersExtractor;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\RequestInterface;
use Magento\Framework\Search\Request\QueryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class IndexBuilder
 * @package Aheadworks\Layerednav\Plugin\CatalogSearch\Search
 */
class IndexBuilder
{
    /**
     * @var FiltersExtractor
     */
    private $filtersExtractor;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var CustomFilterApplier
     */
    private $customFilterApplier;

    /**
     * @var ContextBuilder
     */
    private $contextBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Context|null
     */
    private $context;

    /**
     * @param FiltersExtractor $filtersExtractor
     * @param FilterChecker $filterChecker
     * @param CustomFilterApplier $customFilterApplier
     * @param ContextBuilder $contextBuilder
     * @param LoggerInterface $logger
     */
    public function __construct(
        FiltersExtractor $filtersExtractor,
        FilterChecker $filterChecker,
        CustomFilterApplier $customFilterApplier,
        ContextBuilder $contextBuilder,
        LoggerInterface $logger
    ) {
        $this->filtersExtractor = $filtersExtractor;
        $this->filterChecker = $filterChecker;
        $this->customFilterApplier = $customFilterApplier;
        $this->contextBuilder = $contextBuilder;
        $this->logger = $logger;
    }

    /**
     * Add custom attribute joins
     *
     * @param SearchIndexBuilder $subject
     * @param Select $select
     * @param RequestInterface $request
     * @return Select
     */
    public function afterBuild(
        SearchIndexBuilder $subject,
        Select $select,
        RequestInterface $request
    ) {
        /** @var QueryInterface $query */
        $query = $request->getQuery();
        /** @var FilterInterface[] $filters */
        $filters = $this->filtersExtractor->extractFiltersFromQuery($query);

        foreach ($filters as $filter) {
            if ($this->filterChecker->isCustom($filter)) {
                try {
                    /** @var Context $context */
                    $context = $this->getContext();
                    $this->customFilterApplier->apply($context, $filter, $select);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }

        return $select;
    }

    /**
     * Get context
     *
     * @return Context
     */
    private function getContext()
    {
        if ($this->context === null) {
            $this->context = $this->contextBuilder->build();
        }

        return $this->context;
    }
}
