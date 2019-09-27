/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './processor/default'
], function ($, defaultProcessor) {
    'use strict';

    return {
        processor: defaultProcessor,
        filterRequestParams: [],

        /**
         * Initialize
         *
         * @param {Object} config
         */
        init: function (config) {
            var self = this;

            this.processor = config.processor;
            this.processor.init();
            $.each(this.filterRequestParams, function () {
                self.processor.registerFilterRequestParam(this);
            });
        },

        /**
         * Update params in url and return modified url
         *
         * @param {String} url
         * @param {Object} params
         * @returns {String}
         */
        updateParams: function (url, params) {
            return this.processor.updateParams(url, params);
        },

        /**
         * Remove params from url and return modified url
         *
         * @param {String} url
         * @param {Array} paramNames
         * @returns {String}
         */
        removeParams: function (url, paramNames) {
            return this.processor.removeParams(url, paramNames);
        },

        /**
         * Prepare filter value
         *
         * @param {Array} filterValue
         * @returns {Object}
         */
        prepareFilterValue: function (filterValue) {
            return this.processor.prepareFilterValue(filterValue);
        },

        /**
         * Register filter request param
         *
         * @param {String} paramName
         */
        registerFilterRequestParam: function (paramName) {
            if (this.processor) {
                this.processor.registerFilterRequestParam(paramName);
            } else {
                this.filterRequestParams.push(paramName);
            }
        }
    };
});
