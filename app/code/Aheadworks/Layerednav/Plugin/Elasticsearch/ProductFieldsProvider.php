<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Plugin\Elasticsearch;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\CompositeFieldProvider;
use Psr\Log\LoggerInterface;

/**
 * Class ProductFieldsProvider
 * @package Aheadworks\Layerednav\Plugin\Elasticsearch
 */
class ProductFieldsProvider
{
    /**
     * @var CustomFieldsProvider
     */
    private $customFieldsProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CustomFieldsProvider $customFieldsProvider
     * @param LoggerInterface $logger
     */
    public function __construct(
        CustomFieldsProvider $customFieldsProvider,
        LoggerInterface $logger
    ) {
        $this->customFieldsProvider = $customFieldsProvider;
        $this->logger = $logger;
    }

    /**
     * Add additional custom fields data
     *
     * @param CompositeFieldProvider $subject
     * @param array $result
     * @param array $context
     * @return array
     */
    public function afterGetFields(
        $subject,
        $result,
        $context = []
    ) {
        try {
            $result = array_merge($result, $this->customFieldsProvider->getFields($context));
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        return $result;
    }
}
