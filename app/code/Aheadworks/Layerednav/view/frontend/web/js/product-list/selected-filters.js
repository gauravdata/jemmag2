/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './../url',
    './../updater',
    './../filter/request/bridge',
    './../filter/value'
], function (
    $,
    url,
    updater,
    requestBridge,
    value
) {
    'use strict';

    $.widget('mage.awLayeredNavSelectedFilters', {
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
                'click [data-role=remove-button],[data-role=selected-item]': function (event) {
                    var submitUrl;

                    value.remove($(event.currentTarget).data('item-id'));
                    event.preventDefault();
                    event.stopPropagation();

                    submitUrl = url.getSubmitUrl(value.getPrepared());
                    requestBridge.submit(submitUrl).then(
                        /**
                         * Called after request finishes
                         */
                        function () {
                            updater.update(submitUrl, requestBridge.getResult());
                        }
                    );
                },

                /**
                 * Calls callback when event is triggered
                 */
                'click [data-role=clear-button]': function (event) {
                    var clearUrl = url.getClearUrl();

                    event.preventDefault();
                    requestBridge.submit(clearUrl).then(
                        /**
                         * Called after request finishes
                         */
                        function () {
                            updater.update(clearUrl, requestBridge.getResult());
                        }
                    );
                }
            });
        }
    });

    return $.mage.awLayeredNavSelectedFilters;
});
