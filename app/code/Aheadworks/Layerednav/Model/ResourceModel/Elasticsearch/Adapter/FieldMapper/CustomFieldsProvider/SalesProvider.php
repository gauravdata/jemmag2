<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider;

use Aheadworks\Layerednav\Model\Customer\GroupResolver as CustomerGroupResolver;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class SalesProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider
 */
class SalesProvider extends BaseProvider
{
    /**
     * @var CustomerGroupResolver
     */
    private $customerGroupResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param string $name
     * @param string $type
     * @param CustomerGroupResolver $customerGroupResolver
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        $name,
        $type,
        CustomerGroupResolver $customerGroupResolver,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($name, $type);
        $this->customerGroupResolver = $customerGroupResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields($context)
    {
        $customerGroupIds = $this->customerGroupResolver->getAllCustomerGroupIds();
        /** @var WebsiteInterface[] $websites */
        $websites = $this->storeManager->getWebsites();

        $fields = [];
        foreach ($customerGroupIds as $customerGroupId) {
            foreach ($websites as $website) {
                $fieldName = $this->getFieldName($customerGroupId, $website->getId());
                $fields[$fieldName] = [
                    'type' => $this->type
                ];
            }
        }

        return $fields;
    }

    /**
     * Get field name
     *
     * @param $customerGroupId
     * @param $websiteId
     * @return string
     */
    private function getFieldName($customerGroupId, $websiteId)
    {
        return $this->name . '_' . $customerGroupId . '_' . $websiteId;
    }
}
