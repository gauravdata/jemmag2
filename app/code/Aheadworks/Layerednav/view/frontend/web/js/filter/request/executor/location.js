/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './../executor'
], function ($, Executor) {
    'use strict';

    return $.extend({}, Executor, {
        /**
         * Submit request
         *
         * @param {String} url
         * @returns {Object}
         */
        submit: function (url) {
            var state = {};

            state.title =  window.document.title;
            state.url = url;
            window.history.pushState(state, state.title, url);

            window.location.replace(url);
            return $.Deferred().resolve();
        },

        /**
         * Get request result
         *
         * @returns {Object|null}
         */
        getResult: function () {
            return null;
        }
    });
});
