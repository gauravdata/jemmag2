/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './../url',
    './../updater',
    './request/bridge'
], function ($, url, updater, requestBridge) {
    'use strict';

    $.widget('mage.awLayeredNavFilterReset', {
        options: {
            params: []
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
                 * @param {Event} event
                 */
                'click': function (event) {
                    var resetUrl = url.getResetUrl(this.options.params);

                    event.stopPropagation();
                    requestBridge.submit(resetUrl).then(

                        /**
                         * Called after request finishes
                         */
                        function () {
                            updater.updateAndScrollUpToTop(resetUrl, requestBridge.getResult());
                        }
                    );
                }
            });
        }
    });

    return $.mage.awLayeredNavFilterReset;
});
