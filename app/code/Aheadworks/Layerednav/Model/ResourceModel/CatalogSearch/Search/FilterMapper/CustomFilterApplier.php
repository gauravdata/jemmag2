<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper;

use Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper\CustomFilterApplier\Context;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * Class CustomFilterApplier
 * @package Aheadworks\Layerednav\Model\ResourceModel\CatalogSearch\Search\FilterMapper
 */
class CustomFilterApplier implements FilterApplierInterface
{
    /**
     * @var FilterApplierInterface[]
     */
    private $appliers;

    /**
     * @param FilterApplierInterface[] appliers
     */
    public function __construct(
        array $appliers = []
    ) {
        $this->appliers = $appliers;
    }
    /**
     * {@inheritdoc}
     */
    public function apply(Context $context, FilterInterface $filter, Select $select)
    {
        $filterName = $filter->getName();
        if (!isset($this->appliers[$filterName])) {
            throw new \Exception(__('Filter applier for %1 is not defined', [$filterName]));
        }
        /** @var FilterApplierInterface $applier */
        $applier = $this->appliers[$filterName];
        if (!$applier instanceof FilterApplierInterface) {
            throw new \Exception('Filter applier must implement ' . FilterApplierInterface::class);
        }

        return $applier->apply($context, $filter, $select);
    }
}
