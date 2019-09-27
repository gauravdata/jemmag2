<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Modifier;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Aheadworks\Layerednav\Model\Store\Resolver as StoreResolver;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Store\Model\Store;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Field;
use Aheadworks\Layerednav\Model\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Model\Image\Resolver as ImageResolver;
use Aheadworks\Layerednav\Model\Image\ViewInterface as ImageViewInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;

/**
 * Class Swatches
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class Swatches implements ModifierInterface
{
    /**
     * Path to title inputs in the form metadata array
     */
    const PATH_TO_TITLE_INPUTS = 'swatches_fieldset/children/swatches/children/record/children';

    /**
     * Path to the notice about options for text swatches
     */
    const PATH_TO_NOTICE_FOR_TEXT_SWATCHES = 'frontend_fieldset/children';

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var ImageResolver
     */
    private $imageResolver;

    /**
     * @param StoreResolver $storeResolver
     * @param ArrayManager $arrayManager
     * @param FilterChecker $filterChecker
     * @param ImageResolver $imageResolver
     */
    public function __construct(
        StoreResolver $storeResolver,
        ArrayManager $arrayManager,
        FilterChecker $filterChecker,
        ImageResolver $imageResolver
    ) {
        $this->storeResolver = $storeResolver;
        $this->arrayManager = $arrayManager;
        $this->filterChecker = $filterChecker;
        $this->imageResolver = $imageResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if (isset($data[FilterInterface::CODE])) {
            $data['are_swatches_allowed'] = $this->filterChecker->areSwatchesAllowed($data[FilterInterface::CODE]);
            $data['are_extra_swatches_visible'] = true;
            $data['are_native_visual_swatches_visible'] = false;
            $data['is_swatches_view_mode_selector_disabled'] = false;
        }
        if (isset($data['extension_attributes'])
            && isset($data['extension_attributes']['swatches'])
        ) {
            $data['swatches'] = $data['extension_attributes']['swatches'];
            if (is_array($data['swatches'])) {
                foreach ($data['swatches'] as &$swatchDataRow) {
                    $swatchDataRow[SwatchInterface::IS_DEFAULT] =
                        $swatchDataRow[SwatchInterface::IS_DEFAULT] ? '1' : '0';
                    $swatchDataRow = $this->getPreparedSwatchImageData($swatchDataRow);
                    $swatchDataRow = $this->getPreparedSwatchStorefrontTitlesData($swatchDataRow);
                }
            }
        }

        return $data;
    }

    /**
     * Prepare swatch image data to display inside corresponding component
     *
     * @param array $swatchDataRow
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getPreparedSwatchImageData($swatchDataRow)
    {
        $swatchValue = $swatchColor = $swatchUrl = '';

        if ($this->isColorSelected($swatchDataRow)) {
            $swatchValue = $swatchColor =
                isset($swatchDataRow[SwatchInterface::VALUE])
                    ? $swatchDataRow[SwatchInterface::VALUE]
                    : '';
        } elseif ($this->isImageSelected($swatchDataRow)) {
            $imageViewData = $this->imageResolver->getViewData(
                isset($swatchDataRow[SwatchInterface::IMAGE])
                    ? $swatchDataRow[SwatchInterface::IMAGE]
                    : []
            );
            $swatchDataRow[SwatchInterface::IMAGE] = $imageViewData;
            $swatchValue = isset($imageViewData[ImageViewInterface::FILE_NAME])
                ? $imageViewData[ImageViewInterface::FILE_NAME]
                : '';
            $swatchUrl = isset($imageViewData[ImageViewInterface::URL])
                ? $imageViewData[ImageViewInterface::URL]
                : '';
        }

        $swatchDataRow['swatch'] = $swatchValue;
        $swatchDataRow['swatch_color'] = $swatchColor;
        $swatchDataRow['swatch_url'] = $swatchUrl;
        return $swatchDataRow;
    }

    /**
     * Check if swatch item uses color instead of loaded image
     *
     * @param array $swatchDataRow
     * @return bool
     */
    private function isColorSelected($swatchDataRow)
    {
        return isset($swatchDataRow[SwatchInterface::VALUE])
            && !empty($swatchDataRow[SwatchInterface::VALUE])
            && strpos($swatchDataRow[SwatchInterface::VALUE], '#') !== false;
    }

    /**
     * Check if swatch item uses loaded image instead of color
     *
     * @param array $swatchDataRow
     * @return bool
     */
    private function isImageSelected($swatchDataRow)
    {
        return isset($swatchDataRow[SwatchInterface::IMAGE])
            && is_array($swatchDataRow[SwatchInterface::IMAGE]);
    }

    /**
     * Prepare swatch storefront titles data to display inside corresponding component
     *
     * @param array $swatchDataRow
     * @return array
     */
    private function getPreparedSwatchStorefrontTitlesData($swatchDataRow)
    {
        $storefrontTitlesData =
            isset($swatchDataRow[SwatchInterface::STOREFRONT_TITLES])
                ? $swatchDataRow[SwatchInterface::STOREFRONT_TITLES]
                : [];
        $preparedTitlesData = [];
        foreach ($storefrontTitlesData as $titleDataRow) {
            if (isset($titleDataRow[StoreValueInterface::STORE_ID])
                && isset($titleDataRow[StoreValueInterface::VALUE])
            ) {
                $preparedTitlesData[$titleDataRow[StoreValueInterface::STORE_ID]]
                    = $titleDataRow[StoreValueInterface::VALUE];
            }
        }
        $swatchDataRow[SwatchInterface::STOREFRONT_TITLES] = $preparedTitlesData;
        return $swatchDataRow;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = $this->addSwatchTitleInputsForAllStores($meta);
        return $meta;
    }

    /**
     * Add to the meta array inputs for swatch title per every store view
     *
     * @param array $meta
     * @return array
     */
    private function addSwatchTitleInputsForAllStores($meta)
    {
        $sortedStores = $this->storeResolver->getStoresSortedBySortOrder();
        $optionTitleInputsDataPerStore = [];
        $storeIndex = 0;

        foreach ($sortedStores as $store) {
            $storeIndex++;

            $fieldName = SwatchInterface::STOREFRONT_TITLES . '.' . $store->getId();
            $fieldMetadata = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'dataType' => 'text',
                            'formElement' => 'input',
                            'label' => $store->getName(),
                            'sortOrder' => 30 + $storeIndex,
                        ],
                    ],
                ],
            ];

            if ($this->isAdmin($store)) {
                $fieldMetadata['arguments']['data']['config']['validation'] = [
                    'required-entry' => true,
                ];
            }

            $optionTitleInputsDataPerStore[$fieldName] = $fieldMetadata;
        }

        $meta = $this->arrayManager->set(
            self::PATH_TO_TITLE_INPUTS,
            $meta,
            $optionTitleInputsDataPerStore
        );

        return $meta;
    }

    /**
     * Check if store is related to the backend
     *
     * @param Store $store
     * @return bool
     */
    private function isAdmin($store)
    {
        return $store->getId() == Store::DEFAULT_STORE_ID;
    }
}
