/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './url/processor/bridge'
], function ($, processorBridge) {
    'use strict';

    return {
        currentUrl: window.location.href,
        filterRequestParams: [],
        paramsToRemove: ['_', 'aw_layered_nav_process_output'],
        paramsToRemoveBeforeSend: ['p'],

        /**
         * Register filter request param
         *
         * @param {String} paramName
         */
        registerFilterRequestParam: function (paramName) {
            if ($.inArray(paramName, this.filterRequestParams) == -1) {
                this.filterRequestParams.push(paramName);
            }
            processorBridge.registerFilterRequestParam(paramName);
        },

        /**
         * Set current url
         *
         * @param {String} url
         */
        setCurrentUrl: function (url) {
            this.currentUrl = processorBridge.removeParams(url, this.paramsToRemove);
        },

        /**
         * Get current url
         *
         * @returns {String}
         */
        getCurrentUrl: function () {
            return this.currentUrl;
        },

        /**
         * Get current url with modified request param
         *
         * @param {String} paramName
         * @param {String} paramValue
         * @param {String} defaultValue
         * @returns {String}
         */
        getCurrentUrlWithChangedParam: function (paramName, paramValue, defaultValue) {
            var paramsToUpdate = {},
                url = $.inArray(paramName, this.paramsToRemoveBeforeSend) == -1
                    ? processorBridge.removeParams(this.currentUrl, this._prepareRemoveParams([]))
                    : this.currentUrl;

            if (paramValue == defaultValue) {
                return processorBridge.removeParams(url, [paramName]);
            }
            paramsToUpdate[paramName] = paramValue;

            return processorBridge.updateParams(url, paramsToUpdate);
        },

        /**
         * Get submit url
         *
         * @param {Array} filterValue
         * @returns {String}
         */
        getSubmitUrl: function (filterValue) {
            var url = processorBridge.removeParams(
                this.currentUrl,
                this._prepareRemoveParams(this.filterRequestParams)
            );

            return processorBridge.updateParams(url, processorBridge.prepareFilterValue(filterValue));
        },

        /**
         * Get clear url
         *
         * @returns {String}
         */
        getClearUrl: function () {
            return processorBridge.removeParams(this.currentUrl, this._prepareRemoveParams(this.filterRequestParams));
        },

        /**
         * Get reset url
         *
         * @param {Array} params
         * @returns {String}
         */
        getResetUrl: function (params) {
            return processorBridge.removeParams(
                this.currentUrl,
                this._prepareRemoveParams(params)
            );
        },

        /**
         * Prepare remove params
         *
         * @param {Array} params
         * @returns {Array}
         */
        _prepareRemoveParams: function (params) {
            return this.paramsToRemoveBeforeSend.concat(params);
        }
    };
});
