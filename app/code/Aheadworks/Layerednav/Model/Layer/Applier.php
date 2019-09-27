<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Model\Layer\Filter\Applier\ApplierInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Applier\Pool as FilterApplierPool;
use Magento\Catalog\Model\Layer;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Applier
 * @package Aheadworks\Layerednav\Model\Layer
 */
class Applier
{
    /**
     * @var FilterListResolver
     */
    private $filterListResolver;

    /**
     * @var FilterApplierPool
     */
    private $filterApplierPool;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FilterListResolver $filterListResolver
     * @param FilterApplierPool $filterApplierPool
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        FilterListResolver $filterListResolver,
        FilterApplierPool $filterApplierPool,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->filterListResolver = $filterListResolver;
        $this->filterApplierPool = $filterApplierPool;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * Apply filters
     *
     * @param Layer $layer
     * @return void
     */
    public function applyFilters(Layer $layer)
    {
        try {
            /** @var FilterListAbstract $filterList */
            $filterList = $this->filterListResolver->get();
            foreach ($filterList->getFilters($layer) as $filter) {
                /** @var ApplierInterface $applier */
                $applier = $this->filterApplierPool->getApplier($filter->getType());
                $applier->apply($this->request, $filter);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

        $layer->apply();
    }
}
