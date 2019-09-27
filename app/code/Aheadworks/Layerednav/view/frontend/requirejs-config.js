/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

var config = {
    map: {
        '*': {
            awLayeredNavFilterActions:      'Aheadworks_Layerednav/js/filter/actions',
            awLayeredNavFilterItem:         'Aheadworks_Layerednav/js/filter/item',
            awLayeredNavFilterReset:        'Aheadworks_Layerednav/js/filter/reset',
            awLayeredNavPriceSlider:        'Aheadworks_Layerednav/js/filter/price-slider',
            awLayeredNavPopover:            'Aheadworks_Layerednav/js/popover',
            awLayeredNavCollapse:           'Aheadworks_Layerednav/js/collapse',
            awLayeredNavSelectedFilters:    'Aheadworks_Layerednav/js/product-list/selected-filters',
            productListToolbarForm:         'Aheadworks_Layerednav/js/product-list/toolbar'
        }
    },
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Aheadworks_Layerednav/js/swatch-renderer-mixin': true
            }
        }
    }
};
