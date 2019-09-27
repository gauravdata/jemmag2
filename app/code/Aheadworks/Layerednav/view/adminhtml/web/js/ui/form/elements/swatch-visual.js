/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Swatches/js/form/element/swatch-visual'
], function (Element) {
    'use strict';

    return Element.extend({
        defaults: {
            backgroundColor: '',
            imageUrl: ''
        },

        /**
         * Retrieve style attribute for swatch container
         */
        getStyle: function () {
            var style = {};

            if (this.backgroundColor) {
                style = {
                    'backgroundColor': this.backgroundColor
                };
            } else if (this.imageUrl) {
                style = {
                    'background-image': 'url(' + this.imageUrl + ')',
                    'background-size': 'cover'
                };
            }

            return style;
        }
    });
});
