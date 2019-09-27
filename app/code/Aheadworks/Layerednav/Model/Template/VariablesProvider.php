<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Template;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory;
use Aheadworks\Layerednav\Model\Layer\State as LayerState;

/**
 * Class VariablesProvider
 * @package Aheadworks\Layerednav\Model\Template
 */
class VariablesProvider
{
    /**
     * @var Layer
     */
    private $layer;

    /**
     * @var Factory
     */
    private $dataObjectFactory;

    /**
     * @var LayerState
     */
    private $layerState;

    /**
     * @param Resolver $layerResolver
     * @param Factory $dataObjectFactory
     * @param LayerState $layerState
     */
    public function __construct(
        Resolver $layerResolver,
        Factory $dataObjectFactory,
        LayerState $layerState
    ) {
        $this->layer = $layerResolver->get();
        $this->dataObjectFactory = $dataObjectFactory;
        $this->layerState = $layerState;
    }

    /**
     * Get template variables
     *
     * @return array
     */
    public function getVariables()
    {
        return [
            'category' => $this->getCategoryVar(),
            'urls' => $this->getUrlsVar()
        ];
    }

    /**
     * Get category template variable
     *
     * @return DataObject
     */
    private function getCategoryVar()
    {
        $category = $this->layer->getCurrentCategory();
        return $this->dataObjectFactory->create(
            [
                'name' => $category->getName(),
                'metatitle' => $category->getMetaTitle(),
                'metadescription' => $category->getMetaDescription()
            ]
        );
    }

    /**
     * Get urls template variable
     *
     * @return DataObject
     */
    private function getUrlsVar()
    {
        $allFilters = [];
        $stateItems = $this->layerState->getItems();
        if (!empty($stateItems)) {
            foreach ($stateItems as $item) {
                $allFilters[] = $this->dataObjectFactory->create(
                    [
                        'name' => $item->getFilterItem()->getFilter()->getTitle(),
                        'value' => $item->getFilterItem()->getValue()
                    ]
                );
            }
        }

        return $this->dataObjectFactory->create(
            ['all_filters' => $allFilters]
        );
    }
}
