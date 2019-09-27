/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './default'
], function ($, Processor) {
    'use strict';

    return $.extend({}, Processor, {
        customFilterKeys: ['aw_stock', 'aw_sales', 'aw_new'],

        /**
         * @inheritdoc
         */
        prepareFilterValue: function (filterValue) {
            var result = {},
                self = this;

            $.each(filterValue, function () {
                var value,
                    delimiter;

                if (this.key == 'price'
                    || this.type == 'decimal'
                    || self.customFilterKeys.indexOf(this.key) != -1
                ) {
                    value = this.value;
                    delimiter = '--';
                } else {
                    value = this.value.replace(/-+/g, function (match) {
                        return match + '-';
                    });
                    delimiter = '-';
                }

                if (result.hasOwnProperty(this.key)) {
                    result[this.key] = result[this.key] + delimiter + value;
                } else {
                    result[this.key] = value;
                }
            });

            return result;
        }
    });
});
