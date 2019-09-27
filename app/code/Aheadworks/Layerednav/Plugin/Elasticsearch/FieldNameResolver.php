<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin\Elasticsearch;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName\CustomResolver
    as CustomFieldNameResolver;
use Magento\Customer\Model\Context;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeAdapter;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldName\Resolver\CompositeResolver;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FieldNameResolver
 * @package Aheadworks\Layerednav\Plugin\Elasticsearch
 */
class FieldNameResolver
{
    /**
     * @var CustomFieldNameResolver
     */
    private $customFieldNameResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CustomFieldNameResolver $customFieldNameResolver
     * @param StoreManagerInterface $storeManager
     * @param HttpContext $httpContext
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomFieldNameResolver $customFieldNameResolver,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        LoggerInterface $logger
    ) {
        $this->customFieldNameResolver = $customFieldNameResolver;
        $this->storeManager = $storeManager;
        $this->httpContext = $httpContext;
        $this->logger = $logger;
    }

    /**
     * Get field name
     *
     * @param CompositeResolver $subject
     * @param \Closure $proceed
     * @param AttributeAdapter $attribute
     * @param array $context
     * @return string
     */
    public function aroundGetFieldName(
        $subject,
        \Closure $proceed,
        AttributeAdapter $attribute,
        $context = []
    ) {
        if ($this->customFieldNameResolver->isCanBeProcessed($attribute->getAttributeCode())) {
            try {
                $extendedContext = $context;
                $extendedContext['website_id'] = $this->storeManager->getStore()->getWebsiteId();
                $extendedContext['customer_group_id'] = $this->httpContext->getValue(Context::CONTEXT_GROUP);
                return $this->customFieldNameResolver->getFieldName($attribute->getAttributeCode(), $extendedContext);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }

        return $proceed($attribute, $context);
    }
}
