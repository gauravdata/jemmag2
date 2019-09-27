<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Template;

use Magento\Framework\Filter\Template as TemplateFilter;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class FilterProvider
 * @package Aheadworks\Layerednav\Model\Template
 */
class FilterProvider
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var VariablesProvider
     */
    private $variablesProvider;

    /**
     * @var string
     */
    private $filterClassName;

    /**
     * @var Filter
     */
    private $filterInstance;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param VariablesProvider $variablesProvider
     * @param string $filterClassName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        VariablesProvider $variablesProvider,
        $filterClassName = Filter::class
    ) {
        $this->objectManager = $objectManager;
        $this->variablesProvider = $variablesProvider;
        $this->filterClassName = $filterClassName;
    }

    /**
     * Retrieves filter instance
     *
     * @return Filter
     * @throws \Exception
     */
    public function getFilter()
    {
        if (!$this->filterInstance) {
            $filterInstance = $this->objectManager->create(
                $this->filterClassName,
                ['variables' => $this->variablesProvider->getVariables()]
            );
            if (!$filterInstance instanceof TemplateFilter) {
                throw new \Exception(
                    'Template filter ' . $this->filterClassName . ' does not implement required interface.'
                );
            }
            $this->filterInstance = $filterInstance;
        }
        return $this->filterInstance;
    }
}
