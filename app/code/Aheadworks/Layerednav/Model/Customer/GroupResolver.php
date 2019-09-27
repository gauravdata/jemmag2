<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Customer;

use Magento\Customer\Api\Data\GroupInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class GroupResolver
 * @package Aheadworks\Layerednav\Model\Customer
 */
class GroupResolver
{
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get all customer groups
     *
     * @return GroupInterface[]
     * @throws LocalizedException
     */
    public function getAllCustomerGroups()
    {
        /** @var GroupInterface[] $groups */
        $groups = $this->groupRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        return $groups;
    }

    /**
     * Get all customer group ids
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAllCustomerGroupIds()
    {
        $groups = $this->getAllCustomerGroups();
        $groupIds = [];
        foreach ($groups as $group) {
            $groupIds[] = $group->getId();
        }

        return $groupIds;
    }
}
