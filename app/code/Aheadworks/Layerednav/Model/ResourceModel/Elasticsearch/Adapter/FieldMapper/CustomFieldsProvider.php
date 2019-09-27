<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper;

/**
 * Class CustomFieldsProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper
 */
class CustomFieldsProvider implements FieldsProviderInterface
{
    /**
     * @var FieldsProviderInterface[]
     */
    private $providers;

    /**
     * @param FieldsProviderInterface[] $providers
     */
    public function __construct(
        array $providers = []
    ) {
        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function getFields($context)
    {
        $fieldsData = [];
        foreach ($this->providers as $provider) {
            if (!$provider instanceof FieldsProviderInterface) {
                throw new \Exception('Fields provider must implement ' . FieldsProviderInterface::class);
            }
            $fieldsData = array_merge($fieldsData, $provider->getFields($context));
        }

        return $fieldsData;
    }
}
