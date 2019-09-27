/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './../processor-abstract'
], function ($, Processor) {
    'use strict';

    return $.extend({}, Processor, {
        filterRequestParams: [],

        /**
         * @inheritdoc
         */
        updateParams: function (url, params) {
            var urlData = this._parseUrl(url);

            for (var paramName in params) {
                if (params.hasOwnProperty(paramName)) {
                    urlData.params[paramName] = params[paramName];
                }
            }

            return this._buildUrl(urlData);
        },

        /**
         * @inheritdoc
         */
        removeParams: function (url, paramNames) {
            var urlData = this._parseUrl(url);

            $.each(paramNames, function () {
                if (urlData.params.hasOwnProperty(this)) {
                    delete urlData.params[this];
                }
            });

            return this._buildUrl(urlData);
        },

        /**
         * @inheritdoc
         */
        prepareFilterValue: function (filterValue) {
            var result = {};

            $.each(filterValue, function () {
                if (result.hasOwnProperty(this.key)) {
                    result[this.key] = result[this.key] + ',' + this.value;
                } else {
                    result[this.key] = this.value;
                }
            });

            return result;
        },

        /**
         * @inheritdoc
         */
        registerFilterRequestParam: function (paramName) {
            if ($.inArray(paramName, this.filterRequestParams) == -1) {
                this.filterRequestParams.push(paramName);
            }
        },

        /**
         * Parse url
         *
         * @param {String} url
         * @returns {Object}
         */
        _parseUrl: function (url) {
            var decode = window.decodeURIComponent,
                urlPaths = url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].replace(/#$/, '').split('&') : [],
                paramData = {},
                parameters;

            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined
                    ? decode(parameters[1].replace(/\+/g, '%20'))
                    : '';
            }

            return {baseUrl: baseUrl, params: paramData};
        },

        /**
         * Build url
         *
         * @param {String} urlData
         * @returns {String}
         */
        _buildUrl: function (urlData) {
            var params = $.param(urlData.params);

            return urlData.baseUrl + (params.length ? '?' + params : '');
        }
    });
});
