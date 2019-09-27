<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin\CatalogSearch\Mysql\Filter;

use Aheadworks\Layerednav\Model\Search\Filter\State as FilterState;
use Aheadworks\Layerednav\Model\Search\Request\FilterChecker;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Search\Request\FilterInterface;
use Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor as FilterPreprocessor;
use Magento\Framework\App\ResourceConnection;

/**
 * Class Preprocessor
 * @package Aheadworks\Layerednav\Plugin\CatalogSearch\Mysql\Filter
 */
class Preprocessor
{
    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var FilterState
     */
    private $filterState;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @param FilterChecker $filterChecker
     * @param FilterState $filterState
     * @param ResourceConnection $resource
     */
    public function __construct(
        FilterChecker $filterChecker,
        FilterState $filterState,
        ResourceConnection $resource
    ) {
        $this->filterChecker = $filterChecker;
        $this->filterState = $filterState;
        $this->connection = $resource->getConnection();
    }

    /**
     * Prevent to process custom filters
     *
     * @param FilterPreprocessor $subject
     * @param FilterInterface $filter
     * @param bool $isNegation
     * @param string $query
     * @return string
     */
    public function aroundProcess(
        FilterPreprocessor $subject,
        \Closure $proceed,
        FilterInterface $filter,
        $isNegation,
        $query
    ) {
        if ($this->filterChecker->isCustom($filter)
            || ($this->filterChecker->isBaseCategory($filter) && $this->filterState->isDoNotUseBaseCategoryFlagSet())
        ) {
            return '';
        }

        if ($this->filterChecker->isCategory($filter)) {
            $resultQuery = str_replace(
                $this->connection->quoteIdentifier('category_ids'),
                $this->connection->quoteIdentifier('category_ids_index.category_id'),
                $query
            );
            return $resultQuery;
        }

        return $proceed($filter, $isNegation, $query);
    }
}
