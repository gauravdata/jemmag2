<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\Data\FilterSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Filter CRUD interface
 * @api
 */
interface FilterRepositoryInterface
{
    /**
     * Save filter
     *
     * @param FilterInterface $filter
     * @param int|null $storeId
     * @param bool $isSynchronizationNeeded
     * @return FilterInterface
     * @throws CouldNotSaveException If save fails
     */
    public function save(FilterInterface $filter, $storeId = null, $isSynchronizationNeeded = true);

    /**
     * Retrieve filter by id
     *
     * @param int $filterId
     * @param int|null $storeId
     * @return FilterInterface
     * @throws NoSuchEntityException If filter does not exist
     */
    public function get($filterId, $storeId = null);

    /**
     * Retrieve filter by code
     *
     * @param string $code
     * @param string $type
     * @param int|null $storeId
     * @return FilterInterface
     * @throws NoSuchEntityException If filter does not exist
     */
    public function getByCode($code, $type, $storeId = null);

    /**
     * Retrieve filters matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return FilterSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Delete filter
     *
     * @param FilterInterface $filter
     * @return bool true on success
     * @throws NoSuchEntityException If filter does not exist
     */
    public function delete(FilterInterface $filter);

    /**
     * Delete filter by id
     *
     * @param int $filterId
     * @return bool true on success
     * @throws NoSuchEntityException If filter does not exist
     */
    public function deleteById($filterId);
}
