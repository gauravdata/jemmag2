<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider;

use Aheadworks\Layerednav\Model\Layer\Filter\DataResolver as FilterDataResolver;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider\Custom\OptionsPreparer;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProviderInterface;
use Aheadworks\Layerednav\Model\Layer\Filter\Item\DataBuilderInterface;
use Aheadworks\Layerednav\Model\Layer\FilterInterface;

/**
 * Class Custom
 * @package Aheadworks\Layerednav\Model\Layer\Filter\Item\DataProvider
 */
class Custom implements DataProviderInterface
{
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
        $options = $this->getOptions();
        $optionsCount = $this->filterDataResolver->getFacetedData($filter, $filter->getCode());
        $preparedOptions = $this->optionsPreparer->perform($filter, $options, $optionsCount, true);

        /** @var array $option */
        foreach ($preparedOptions as $option) {
            $this->itemDataBuilder->addItemData($option['label'], $option['value'], $option['count'], $option['image']);
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * Get options
     *
     * @return array
     */
    private function getOptions()
    {
        return [
            [
                'value' => FilterInterface::CUSTOM_FILTER_VALUE_YES,
                'label' => __('Yes')
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getStatisticsData($filter)
    {
        return [];
    }
}
