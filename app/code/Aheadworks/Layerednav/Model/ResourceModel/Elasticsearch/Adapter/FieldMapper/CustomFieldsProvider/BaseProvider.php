<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider;

use Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\FieldsProviderInterface;

/**
 * Class BaseProvider
 * @package Aheadworks\Layerednav\Model\ResourceModel\Elasticsearch\Adapter\FieldMapper\CustomFieldsProvider
 */
class BaseProvider implements FieldsProviderInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $name
     * @param string $type
     */
    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields($context)
    {
        return [
            $this->name => [
                'type' => $this->type
            ]
        ];
    }
}
