/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'mage/utils/strings',
    'Magento_Ui/js/grid/columns/column'
], function (stringUtils, Column) {
    'use strict';

    return Column.extend({
        /**
         * @inheritdoc
         */
        getLabel: function (row) {
            var number = this._super(row);

            return String(Number(number * 1).toFixed(2)) + '%';
        },
    });
});