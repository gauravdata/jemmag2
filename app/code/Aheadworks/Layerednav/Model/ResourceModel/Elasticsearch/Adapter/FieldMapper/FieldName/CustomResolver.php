<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName;

/**
 * Class CustomResolver
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldName
 */
class CustomResolver implements CustomResolverInterface
{
    /**
     * @var CustomResolverInterface[]
     */
    private $resolvers;

    /**
     * @param CustomResolverInterface[] $resolvers
     */
    public function __construct(
        array $resolvers = []
    ) {
        $this->resolvers = $resolvers;
    }

    /**
     * Get field name
     *
     * @param string $attributeCode
     * @param array $context
     * @return string
     * @throws \Exception
     */
    public function getFieldName($attributeCode, $context = [])
    {
        if (!isset($this->resolvers[$attributeCode])) {
            throw new \Exception("Custom field can't be processed");
        }
        $customResolver = $this->resolvers[$attributeCode];
        if (!$customResolver instanceof CustomResolverInterface) {
            throw new \Exception('Custom field name resolver must implement ' . CustomResolverInterface::class);
        }
        return $customResolver->getFieldName($attributeCode, $context);
    }

    /**
     * Is can be processed
     *
     * @param string $attributeCode
     * @return bool
     */
    public function isCanBeProcessed($attributeCode)
    {
        return isset($this->resolvers[$attributeCode]);
    }
}
