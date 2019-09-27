/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    './../url',
    './../filter/request/bridge',
    './../updater',
    'Magento_Catalog/js/product/list/toolbar'
], function($, _, url, requestBridge, updater) {
    'use strict';

    $.widget('mage.awLayeredNavToolbar', $.mage.productListToolbarForm, {
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
            var handlers = {};

            handlers = this._addEventHandler(
                handlers,
                this.options.modeControl,
                this.options.mode,
                this.options.modeDefault
            );
            handlers = this._addEventHandler(
                handlers,
                this.options.directionControl,
                this.options.direction,
                this.options.directionDefault
            );
            handlers = this._addEventHandler(
                handlers,
                this.options.orderControl,
                this.options.order,
                this.options.orderDefault
            );
            handlers = this._addEventHandler(
                handlers,
                this.options.limitControl,
                this.options.limit,
                this.options.limitDefault,
                '_limitHandler'
            );

            this._on(handlers);
        },

        /**
         * Add event handler
         *
         * @param {Object} handlers
         * @param {String} selector
         * @param {String} paramName
         * @param {String} defaultValue
         * @param {String|Undefined} customHandler
         * @returns {Object}
         */
        _addEventHandler: function (handlers, selector, paramName, defaultValue, customHandler) {
            var isSelect = $(selector).is('select'),
                event = isSelect ? 'change' : 'click';

            handlers[event + ' ' + selector] = _.isFunction(this[customHandler])
                ? function (event) {
                    this[customHandler](event, paramName, defaultValue, isSelect)
                }
                : (isSelect
                    ? function (event) {
                        this._processSelect(event, paramName, defaultValue);
                    }
                    : function (event) {
                        this._processLink(event, paramName, defaultValue);
                    });

            return handlers;
        },

        /**
         * Process click on link
         *
         * @param {Object} event
         * @param {String} paramName
         * @param {String} defaultValue
         * @param {Boolean} isSelect
         */
        _limitHandler: function (event, paramName, defaultValue, isSelect) {
            var paramValue = isSelect
                    ? event.currentTarget.options[event.currentTarget.selectedIndex].value
                    : $(event.currentTarget).data('value');

            event.preventDefault();
            this.changeUrl(
                paramName,
                paramValue,
                defaultValue,
                '_afterSubmitLimit'
            );
        },

        /**
         * Process click on link
         *
         * @param {Object} event
         * @param {String} paramName
         * @param {String} defaultValue
         */
        _processLink: function (event, paramName, defaultValue) {
            event.preventDefault();
            this.changeUrl(
                paramName,
                $(event.currentTarget).data('value'),
                defaultValue
            );
        },

        /**
         * Process select change
         *
         * @param {Object} event
         * @param {String} paramName
         * @param {String} defaultValue
         */
        _processSelect: function (event, paramName, defaultValue) {
            this.changeUrl(
                paramName,
                event.currentTarget.options[event.currentTarget.selectedIndex].value,
                defaultValue
            );
        },

        /**
         * Change current url
         *
         * @param {String} paramName
         * @param {String} paramValue
         * @param {String} defaultValue
         * @param {String|Undefined} afterSubmit
         */
        changeUrl: function (paramName, paramValue, defaultValue, afterSubmit) {
            var updateUrl = url.getCurrentUrlWithChangedParam(paramName, paramValue, defaultValue);

            afterSubmit = afterSubmit || '_afterSubmitDefault';
            requestBridge.submit(updateUrl).then(
                /**
                 * Called after request finishes
                 */
                function () {
                    this[afterSubmit](updateUrl)
                }.bind(this)
            );
        },

        /**
         * Called after request finishes
         *
         * @param {String} updateUrl
         */
        _afterSubmitDefault: function (updateUrl) {
            updater.update(updateUrl, requestBridge.getResult());
        },

        /**
         * Called after request finishes
         *
         * @param {String} updateUrl
         */
        _afterSubmitLimit: function (updateUrl) {
            updater.updateAndScrollUpToTop(updateUrl, requestBridge.getResult());
        }
    });

    return $.mage.awLayeredNavToolbar;
});
