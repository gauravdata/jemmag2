/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    var valueItems = {};

    return {
        config: {
            popover: '[data-role=aw-layered-nav-popover]'
        },

        /**
         * Initialize
         *
         * @param {Object} config
         */
        init: function (config) {
            $.extend(this.config, config);
        },

        /**
         * Add filter item value
         *
         * @param {String} id
         * @param {String} name
         * @param {String} value
         * @param {String} type
         */
        add: function (id, name, value, type) {
            valueItems[id] = {
                key: name,
                value: value,
                type: type
            };
            this._triggerEvent();
        },

        /**
         * Add filter item value (exclusive)
         *
         * @param {String} id
         * @param {String} name
         * @param {String} value
         * @param {String} type
         */
        addExclusive: function (id, name, value, type) {
            for (var index in valueItems) {
                if (valueItems[index].key == name) {
                    delete valueItems[index];
                }
            }
            valueItems[id] = {
                key: name,
                value: value,
                type: type
            };
            this._triggerEvent();
        },

        /**
         * Remove filter item value
         *
         * @param {String} id
         */
        remove: function (id) {
            delete valueItems[id];
            this._triggerEvent();
        },

        /**
         * Reset filter item value
         */
        reset: function () {
            valueItems = [];
        },

        /**
         * Get prepared filter values
         *
         * @returns {Array}
         */
        getPrepared: function () {
            var value = [];

            for (var id in valueItems) {
                if (valueItems.hasOwnProperty(id)) {
                    value.push(valueItems[id]);
                }
            }

            return value;
        },

        /**
         * Trigger filter value change event
         */
        _triggerEvent: function () {
            $(this.config.popover).trigger('awLayeredNav:filterValueChange', [this.getPrepared()]);
        }
    };
});
