/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        getName: function(row) {
            return row[this.index];
        },
        getUrl: function(row) {
            return row[this.index + '_url'];
        },
        hasLink:  function(row) {
            var key = this.index + '_url';
            return key in row;
        }
    });
});
