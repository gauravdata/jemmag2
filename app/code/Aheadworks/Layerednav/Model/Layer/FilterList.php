<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Filter\CustomFilterChecker;
use Aheadworks\Layerednav\Model\Layer\FilterInterface as LayerFilterInterface;
use Aheadworks\Layerednav\Model\Layer\FilterFactory as LayerFilterFactory;
use Aheadworks\Layerednav\Model\Filter\CategoryValidator as FilterCategoryValidator;
use Aheadworks\Layerednav\Model\Layer\FilterList\AttributeProviderInterface;
use Aheadworks\Layerednav\Model\Layer\FilterList\FilterProviderInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer;
use Psr\Log\LoggerInterface;

/**
 * Class FilterList
 * @package Aheadworks\Layerednav\Model\Layer
 */
class FilterList
{
    /**
     * @var LayerFilterFactory
     */
    private $layerFilterFactory;

    /**
     * @var FilterProviderInterface
     */
    private $filterProvider;

    /**
     * @var AttributeProviderInterface
     */
    private $attributeProvider;

    /**
     * @var CustomFilterChecker
     */
    private $customFilterChecker;

    /**
     * @var FilterCategoryValidator
     */
    private $filterCategoryValidator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var LayerFilterInterface[]
     */
    private $filters;

    /**
     * @param FilterFactory $layerFilterFactory
     * @param FilterProviderInterface $filterProvider
     * @param AttributeProviderInterface $attributeProvider
     * @param CustomFilterChecker $customFilterChecker
     * @param FilterCategoryValidator $filterCategoryValidator
     * @param LoggerInterface $logger
     */
    public function __construct(
        LayerFilterFactory $layerFilterFactory,
        FilterProviderInterface $filterProvider,
        AttributeProviderInterface $attributeProvider,
        CustomFilterChecker $customFilterChecker,
        FilterCategoryValidator $filterCategoryValidator,
        LoggerInterface $logger
    ) {
        $this->layerFilterFactory = $layerFilterFactory;
        $this->filterProvider = $filterProvider;
        $this->attributeProvider = $attributeProvider;
        $this->customFilterChecker = $customFilterChecker;
        $this->filterCategoryValidator = $filterCategoryValidator;
        $this->logger = $logger;
    }

    /**
     * Get filters
     *
     * @param Layer $layer
     * @return LayerFilterInterface[]
     */
    public function getFilters(Layer $layer)
    {
        if (!$this->filters) {
            $filters = [];

            try {
                /** @var FilterInterface[] $filterObjects */
                $filterObjects = $this->filterProvider->getFilterDataObjects();
                $filterableAttributes = $this->attributeProvider->getAttributes();
                /** @var Category $currentCategory */
                $currentCategory = $layer->getCurrentCategory();

                foreach ($filterObjects as $filterObject) {
                    if ($this->filterCategoryValidator->validate($filterObject, $currentCategory)) {
                        if ($this->customFilterChecker->isCustom($filterObject->getType())) {
                            if ($this->customFilterChecker->isAvailable($filterObject->getType())) {
                                $filters[] = $this->layerFilterFactory->create($filterObject, $layer);
                            }
                        } else {
                            $attributeCode = $filterObject->getCode();
                            if (array_key_exists($attributeCode, $filterableAttributes)) {
                                $attribute = $filterableAttributes[$attributeCode];
                                $filters[] = $this->layerFilterFactory->create($filterObject, $layer, $attribute);
                                unset($filterableAttributes[$attributeCode]);
                            }
                        }
                    }
                }
            } catch (\Exception $exception) {
                $this->logger->critical($exception->getMessage());
                $filters = [];
            }

            $this->filters = $filters;
        }
        return $this->filters;
    }
}
