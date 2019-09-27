/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './default',
    './../../../filter/value'
], function ($, Processor, value) {
    'use strict';

    return $.extend({}, Processor, {
        customFilterKeys: ['aw_stock', 'aw_sales', 'aw_new'],
        value: [],
        reservedNonFilterableParams: ['_', 'aw_layered_nav_process_output'],

        /**
         * @inheritdoc
         */
        updateParams: function (url, params) {
            var urlData = this._fullParseUrl(url),
                self = this;

            for (var paramName in params) {
                var key = self.filterRequestParams.indexOf(paramName) != -1
                    ? 'filterParams'
                    : 'params',
                    param = (paramName == 'cat' ? 'category' : paramName);

                if (params.hasOwnProperty(paramName)) {
                    urlData[key][param] = params[paramName];
                }
            }

            return this._buildUrl(urlData);
        },

        /**
         * @inheritdoc
         */
        removeParams: function (url, paramNames) {
            var urlData = {},
                self = this,
                isNonFilterableOnly = true;

            $.each(paramNames, function () {
                if (self.reservedNonFilterableParams.indexOf(this) == -1) {
                    isNonFilterableOnly = false;
                }
            });

            urlData = isNonFilterableOnly
                ? this._parseUrl(url)
                : this._fullParseUrl(url);
            $.each(paramNames, function () {
                var key = self.filterRequestParams.indexOf(this) != -1
                    ? 'filterParams'
                    : 'params',
                    paramName = (this == 'cat' ? 'category' : this);

                if (urlData[key].hasOwnProperty(paramName)) {
                    delete urlData[key][paramName];
                }
            });

            return this._buildUrl(urlData);
        },

        /**
         * @inheritdoc
         */
        prepareFilterValue: function (filterValue) {
            var result = {},
                self = this;

            $.each(filterValue, function () {
                var value = self._prepareFilterOptionValue(this),
                    delimiter = this.key == 'price' || this.type == 'decimal'
                        ? '--'
                        : '-';

                if (result.hasOwnProperty(this.key)) {
                    result[this.key] = result[this.key] + delimiter + value;
                } else {
                    result[this.key] = value;
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
            this.value = value.getPrepared();
        },

        /**
         * Parse url including divide into filterable and non-filterable params
         *
         * @param {String} url
         * @returns {Object}
         */
        _fullParseUrl: function (url) {
            var decode = window.decodeURIComponent,
                urlPaths = url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].replace(/#$/, '').split('&') : [],
                paramData = {},
                filterParamData = {},
                filterParamsCandidates = [],
                parameters,
                self = this;

            $.each(this.value, function () {
                var paramCandidate = (new RegExp(self._getFindFilterParamsPattern(this.key, this.value)))
                    .exec(urlPaths[0]),
                    paramName;

                if (paramCandidate) {
                    paramName = self._isCustomFilter(this.key)
                        ? this.key
                        : paramCandidate[1];
                    if (filterParamsCandidates.indexOf(paramName) == -1) {
                        filterParamsCandidates.push(paramName);
                    }
                    if (!filterParamData.hasOwnProperty(paramName)) {
                        filterParamData[paramName] = [];
                    }
                    filterParamData[paramName].push(self._prepareFilterOptionValue(this));
                }
            });
            $.each(filterParamsCandidates, function () {
                var paramValues = (new RegExp(self._getParseFilterValuesPattern(this, filterParamData[this])))
                    .exec(urlPaths[0]);

                if (paramValues) {
                    filterParamData[this] = paramValues[1];
                    baseUrl = baseUrl.replace(
                        new RegExp(self._getCutFilterParamsPattern(this, paramValues[1])),
                        ''
                    );
                }
            });

            for (var i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined
                    ? decode(parameters[1].replace(/\+/g, '%20'))
                    : '';
            }

            return {
                baseUrl: baseUrl,
                params: paramData,
                filterParams: filterParamData
            };
        },

        /**
         * Get find filter params pattern
         *
         * @param {String} key
         * @param {String} value
         * @returns {String}
         */
        _getFindFilterParamsPattern: function (key, value) {
            return this._isCustomFilter(key)
                ? '(' + value + ')'
                : '(' + (key == 'cat' ? 'category' : key) + ')-';
        },

        /**
         * Get parse filter value pattern
         *
         * @param {String} key
         * @param {Array} values
         * @returns {String}
         */
        _getParseFilterValuesPattern: function (key, values) {
            var type,
                valuesSet,
                result = '(';

            values.sort(function(a, b){
                return b.length - a.length;
            })

            valuesSet = '(' + values.join('|') + ')';

            $.each(this.value, function () {
                if (this.key == key) {
                    type = this.type;
                    return false;
                }
            });
            for (var i = 0; i < values.length; i++) {
                result += valuesSet + (i == 0 ? '{1}' : '{0,1}');
                if (i < values.length - 1) {
                    result += (key == 'price' || (type && type == 'decimal') ? '(--){0,1}' : '-{0,1}');
                }
            }
            result += ')';
            if (!this._isCustomFilter(key)) {
                result = (key == 'cat' ? 'category' : key) + '-' + result;
            }

            return result;
        },

        /**
         * Get cut from url string filter param pattern
         *
         * @param {String} key
         * @param {String} value
         * @returns {String}
         */
        _getCutFilterParamsPattern: function (key, value) {
            return '/' + (this._isCustomFilter(key) ? value : key + '-' + value);
        },

        /**
         * Prepare filter option value
         *
         * @param {Object} filterValue
         * @returns {String}
         */
        _prepareFilterOptionValue: function (filterValue) {
            return (filterValue.key == 'price'
                || filterValue.type == 'decimal'
                || this._isCustomFilter(filterValue.key))
                    ? filterValue.value
                    : filterValue.value.replace(/-+/g, function (match) {
                        return match + '-';
                    });
        },

        /**
         * Check if value key corresponds to custom filter
         *
         * @param {String} key
         * @returns {boolean}
         */
        _isCustomFilter: function (key) {
            return this.customFilterKeys.indexOf(key) != -1;
        },

        /**
         * Build url
         *
         * @param {Object} urlData
         * @returns {String}
         */
        _buildUrl: function (urlData) {
            var params = $.param(urlData.params),
                filterParams = '',
                self = this;

            if (urlData.filterParams) {
                $.each(urlData.filterParams, function (key) {
                    filterParams += '/' + (self._isCustomFilter(key) ? this : key + '-' + this);
                });
            }

            return urlData.baseUrl.replace(/[\\/]+$/g, '')
                + filterParams
                + (params.length ? '?' + params : '');
        }
    });
});
