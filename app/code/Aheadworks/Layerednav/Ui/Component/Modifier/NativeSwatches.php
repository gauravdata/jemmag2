<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Modifier;

use Aheadworks\Layerednav\Api\Data\FilterInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Aheadworks\Layerednav\Model\Filter\Checker as FilterChecker;
use Aheadworks\Layerednav\Api\Data\Filter\SwatchInterface;
use Aheadworks\Layerednav\Api\Data\StoreValueInterface;
use Magento\Swatches\Helper\Media as SwatchesMediaHelper;
use Magento\Store\Model\Store;
use Aheadworks\Layerednav\Model\Store\Resolver as StoreResolver;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Field;
use Aheadworks\Layerednav\Model\Product\Attribute\Resolver as ProductAttributeResolver;
use Aheadworks\Layerednav\Model\Source\Filter\SwatchesMode as FilterSwatchesModeSourceModel;

/**
 * Class NativeSwatches
 *
 * @package Aheadworks\Layerednav\Ui\Component\Modifier
 */
class NativeSwatches implements ModifierInterface
{
    /**
     * Path to title inputs in the form metadata array
     */
    const PATH_TO_TITLE_INPUTS = 'swatches_fieldset/children/native_visual_swatches/children/record/children';

    /**
     * @var FilterChecker
     */
    private $filterChecker;

    /**
     * @var SwatchesMediaHelper
     */
    private $swatchesMediaHelper;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var ProductAttributeResolver
     */
    private $productAttributeResolver;

    /**
     * @param FilterChecker $filterChecker
     * @param SwatchesMediaHelper $swatchesMediaHelper
     * @param StoreResolver $storeResolver
     * @param ArrayManager $arrayManager
     * @param ProductAttributeResolver $productAttributeResolver
     */
    public function __construct(
        FilterChecker $filterChecker,
        SwatchesMediaHelper $swatchesMediaHelper,
        StoreResolver $storeResolver,
        ArrayManager $arrayManager,
        ProductAttributeResolver $productAttributeResolver
    ) {
        $this->filterChecker = $filterChecker;
        $this->swatchesMediaHelper = $swatchesMediaHelper;
        $this->storeResolver = $storeResolver;
        $this->arrayManager = $arrayManager;
        $this->productAttributeResolver = $productAttributeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $data['are_text_swatches_used'] = false;
        if (isset($data[FilterInterface::CODE])) {
            $areSwatchesAlreadyAllowed = isset($data['are_swatches_allowed']) ? $data['are_swatches_allowed'] : false;
            $data['are_swatches_allowed'] =
                $areSwatchesAlreadyAllowed
                || $this->filterChecker->areNativeVisualSwatchesUsed($data[FilterInterface::CODE]);

            $data['are_text_swatches_used'] = $this->filterChecker->areNativeTextSwatchesUsed(
                $data[FilterInterface::CODE]
            );
            $data['text_swatches_notice'] = __(
                '<a href="%1">Click here</a> to manage the values of this attribute.',
                $this->productAttributeResolver->getBackendEditLinkByCode($data[FilterInterface::CODE])
            );
            if ($this->filterChecker->areNativeVisualSwatchesUsed($data[FilterInterface::CODE])) {
                $data = $this->processDataToPrepareFormForNativeVisualSwatches($data);
            }
        }

        $data = $this->processNativeVisualSwatchesData($data);
        return $data;
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
     * Process data to prepare form for correct processing of native visual swatches
     *
     * @param array $data
     * @return array
     */
    private function processDataToPrepareFormForNativeVisualSwatches($data)
    {
        $data['are_extra_swatches_visible'] = false;
        $data['are_native_visual_swatches_visible'] = true;
        $data[FilterInterface::SWATCHES_VIEW_MODE] = FilterSwatchesModeSourceModel::IMAGE_ONLY;
        $data['is_swatches_view_mode_selector_disabled'] = true;
        $data['swatches'] = [];
        return $data;
    }

    /**
     * Process native visual swatches data
     *
     * @param array $data
     * @return array
     */
    private function processNativeVisualSwatchesData($data)
    {
        if (isset($data['extension_attributes'])
            && isset($data['extension_attributes']['native_visual_swatches'])
        ) {
            $data['native_visual_swatches'] = $data['extension_attributes']['native_visual_swatches'];
            if (is_array($data['native_visual_swatches'])) {
                foreach ($data['native_visual_swatches'] as &$swatchDataRow) {
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
     * Prepare native visual swatch image data to display inside corresponding component
     *
     * @param array $swatchDataRow
     * @return array
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
            $fileName = isset($swatchDataRow[SwatchInterface::VALUE])
                ? $swatchDataRow[SwatchInterface::VALUE]
                : '';
            $swatchValue = $fileName;
            $swatchUrl = $this->swatchesMediaHelper->getSwatchMediaUrl() . $fileName;
        }

        $swatchDataRow['swatch'] = $swatchValue;
        $swatchDataRow['swatch_color'] = $swatchColor;
        $swatchDataRow['swatch_url'] = $swatchUrl;
        return $swatchDataRow;
    }

    /**
     * Check if native visual swatch item uses color instead of loaded image
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
     * Check if native visual swatch item uses loaded image instead of color
     *
     * @param array $swatchDataRow
     * @return bool
     */
    private function isImageSelected($swatchDataRow)
    {
        return isset($swatchDataRow[SwatchInterface::VALUE])
            && !empty($swatchDataRow[SwatchInterface::VALUE])
            && strpos($swatchDataRow[SwatchInterface::VALUE], '#') === false
            && strpos($swatchDataRow[SwatchInterface::VALUE], '/') !== false;
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
