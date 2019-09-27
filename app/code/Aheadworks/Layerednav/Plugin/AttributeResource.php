<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Api\FilterRepositoryInterface;
use Aheadworks\Layerednav\Api\FilterManagementInterface;
use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Layerednav\Model\FilterManagement;

/**
 * Class AttributeResource
 * @package Aheadworks\Layerednav\Plugin
 */
class AttributeResource
{
    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var FilterManagementInterface
     */
    private $filterManagement;

    /**
     * @param FilterRepositoryInterface $filterRepository
     * @param FilterManagementInterface $filterManagement
     */
    public function __construct(
        FilterRepositoryInterface $filterRepository,
        FilterManagementInterface $filterManagement
    ) {
        $this->filterRepository = $filterRepository;
        $this->filterManagement = $filterManagement;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Attribute $subject
     * @param \Closure $proceed
     * @param EavAttributeInterface $attribute
     * @return \Magento\Framework\Model\AbstractModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Catalog\Model\ResourceModel\Attribute $subject,
        \Closure $proceed,
        EavAttributeInterface $attribute
    ) {
        $result = $proceed($attribute);

        $noNeedToSynchronizeFilterFlag = $attribute->getData(FilterManagement::NO_NEED_TO_SYNCHRONIZE_FILTER_FLAG);
        if ($noNeedToSynchronizeFilterFlag) {
            return $result;
        }

        $filterType = $this->filterManagement->getAttributeFilterType($attribute);
        try {
            /** @var FilterInterface $filter */
            $filter = $this->filterRepository->getByCode($attribute->getAttributeCode(), $filterType);
            if ($this->filterManagement->isSyncNeeded($filter, $attribute)) {
                $this->filterManagement->synchronizeFilter($filter, $attribute);
            }
        } catch (NoSuchEntityException $e) {
            $this->filterManagement->createFilter($attribute);
        }

        return $result;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Attribute $subject
     * @param \Closure $proceed
     * @param EavAttributeInterface $attribute
     * @return \Magento\Framework\Model\AbstractModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDelete(
        \Magento\Catalog\Model\ResourceModel\Attribute $subject,
        \Closure $proceed,
        EavAttributeInterface $attribute
    ) {
        $filterType = $this->filterManagement->getAttributeFilterType($attribute);
        try {
            /** @var FilterInterface $filter */
            $filter = $this->filterRepository->getByCode($attribute->getAttributeCode(), $filterType);
        } catch (NoSuchEntityException $e) {
            // do nothing
        }

        $result = $proceed($attribute);

        if (isset($filter) && $filter->getId()) {
            $this->filterRepository->delete($filter);
        }

        return $result;
    }
}
