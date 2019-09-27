/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './../url',
    './../filter/request/bridge',
    './../updater',
    'jquery/ui'
], function($, url, requestBridge, updater) {
    'use strict';

    $.widget('mage.awLayeredNavPager', {
        options: {
            pageVarName: 'p'
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function () {
            this._on({

                /**
                 * Calls callback when event is triggered
                 */
                'click a[href]': function (event) {
                    var updateUrl = this._getUpdateUrl($(event.currentTarget).attr('href'));

                    event.preventDefault();
                    requestBridge.submit(updateUrl).then(
                        /**
                         * Called after request finishes
                         */
                        function () {
                            updater.updateAndScrollUpToTop(updateUrl, requestBridge.getResult());
                        }
                    );
                }
            });
        },

        /**
         * Get update url
         *
         * @param {String} linkUrl
         * @returns {String}
         */
        _getUpdateUrl: function (linkUrl) {
            var decode = window.decodeURIComponent,
                urlPaths = linkUrl.split('?'),
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                self = this,
                pageValue = null;

            $.each(urlParams, function () {
                var parameters = this.split('=');

                if (decode(parameters[0]) == self.options.pageVarName) {
                    pageValue = decode(parameters[1].replace(/\+/g, '%20'));
                }
            });

            return pageValue
                ? url.getCurrentUrlWithChangedParam(this.options.pageVarName, pageValue, '1')
                : url.getCurrentUrl();
        }
    });

    return $.mage.awLayeredNavPager;
});
