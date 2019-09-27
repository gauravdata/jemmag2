/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    './executor/location'
], function ($, _, defaultExecutor) {
    'use strict';

    return {
        executor: defaultExecutor,

        /**
         * Initialize
         *
         * @param {Object} config
         */
        init: function (config) {
            this.executor = config.executor;
            this._initHistoryListener();
        },

        /**
         * Initialize history listener
         */
        _initHistoryListener: function () {
            var state = {};

            if (_.isEmpty(window.history.state)) {
                state.title =  window.document.title;
                state.url = location.href;
                window.history.replaceState(state, state.title, state.url);
            }

            window.history.scrollRestoration = 'manual';

            $(window).off('popstate');
            $(window).on('popstate', function(event) {
                var originalEvent = event.originalEvent;

                if (!_.isEmpty(originalEvent.state)) {
                    window.location.replace(originalEvent.state.url);
                    $.Deferred().resolve();
                }
            }.bind(this));
        },

        /**
         * Submit request
         *
         * @param {String} url
         * @returns {Object}
         */
        submit: function (url) {
            return this.executor.submit(url);
        },

        /**
         * Get request result
         *
         * @returns {Object|null}
         */
        getResult: function () {
            return this.executor.getResult();
        }
    };
});
