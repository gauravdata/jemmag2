<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Attribute\OptionsPreparer;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;

/**
 * Class Attribute
 * @package \Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider
 */
class Attribute implements DataProviderInterface
{
    /**
     * Value of attribute options only with results
     */
    const ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS = 1;

    /**
     * @var FilterDataResolver
     */
    private $filterDataResolver;

    /**
     * @var OptionsPreparer
     */
    private $optionsPreparer;

    /**
     * @var DataBuilderInterface
     */
    private $itemDataBuilder;

    /**
     * @param FilterDataResolver $filterDataResolver
     * @param OptionsPreparer $optionsPreparer
     * @param DataBuilderInterface $itemDataBuilder
     */
    public function __construct(
        FilterDataResolver $filterDataResolver,
        OptionsPreparer $optionsPreparer,
        DataBuilderInterface $itemDataBuilder
    ) {
        $this->filterDataResolver = $filterDataResolver;
        $this->optionsPreparer = $optionsPreparer;
        $this->itemDataBuilder = $itemDataBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsData($filter)
    {
        $attribute = $filter->getAttributeModel();
        $options = $attribute->getFrontend()->getSelectOptions();
        $optionsCount = $this->filterDataResolver->getFacetedData($filter);
        $withCountOnly = (int)$attribute->getIsFilterable() == self::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS;

        $preparedOptions = $this->optionsPreparer->perform($filter, $options, $optionsCount, $withCountOnly);

        /** @var array $option */
        foreach ($preparedOptions as $option) {
            $this->itemDataBuilder->addItemData($option['label'], $option['value'], $option['count'], $option['image']);
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * {@inheritdoc}
     */
    public function getStatisticsData($filter)
    {
        return [];
    }
}
