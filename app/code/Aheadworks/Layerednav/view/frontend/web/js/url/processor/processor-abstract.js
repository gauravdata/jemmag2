/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([], function () {
    'use strict';

    return {
        /**
         * Init url processor
         */
        init: function () {},

        /**
         * Update params in url and return modified url
         *
         * @param {String} url
         * @param {Object} params
         * @returns {String}
         */
        updateParams: function (url, params) {},

        /**
         * Remove params from url and return modified url
         *
         * @param {String} url
         * @param {Array} paramNames
         * @returns {String}
         */
        removeParams: function (url, paramNames) {},

        /**
         * Prepare filter value
         *
         * @param {Array} filterValue
         * @returns {Object}
         */
        prepareFilterValue: function (filterValue) {},

        /**
         * Register filter request param
         *
         * @param {String} paramName
         */
        registerFilterRequestParam: function (paramName) {}
    };
});
