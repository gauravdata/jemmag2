/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './value',
    './item/current',
    './../url'
], function ($, filterValue, currentFilterItem, url) {
    'use strict';

    $.widget('mage.awLayeredNavFilterItem', {
        options: {
            type: 'default',
            backendType: '',
            popover: '[data-role=aw-layered-nav-popover]',
            multiSelectMode: true,
            itemsSelector: '.items',
            activeClass: 'active'
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._bind();
            if (this._isSelectedInitially(this.element)) {
                this._updateValue(this.element);
                if (this.options.type == 'swatch') {
                    $(document).trigger('awLayeredNav:initSwatchValues');
                }
            } else if (this._isSelected(this.element)) {
                currentFilterItem.set(this.element);
                this._triggerClickEvent(this.element);
                this._updateValue(this.element);
            }
            url.registerFilterRequestParam(
                this._getInputElement(this.element).attr('name')
            );
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
                    var element = $(event.target);

                    if (this.options.type == 'swatch' && !$(element).hasClass('disabled')) {
                        $(element).toggleClass(this.options.activeClass);
                    }

                    if (!this.options.multiSelectMode) {
                        $(element).closest(this.options.itemsSelector).prev().removeClass(this.options.activeClass);
                        $(element)
                            .closest(this.options.itemsSelector)
                            .children()
                            .not(element)
                            .removeClass(this.options.activeClass);
                    }

                    if (this._isEnabled(element)) {
                        currentFilterItem.set(element);
                        this._triggerClickEvent(element);
                        this._updateValue(element);
                    }
                }
            });
        },

        /**
         * Check if element selected
         *
         * @param {Object} element
         * @returns {Boolean}
         */
        _isSelected: function (element) {
            var result = element.is(':checked');

            if (this.options.type == 'swatch') {
                result = element.hasClass('active');
            }

            return result
        },

        /**
         * Check if element after page load
         *
         * @param {Object} element
         * @returns {Boolean}
         */
        _isSelectedInitially: function (element) {
            var result = element[0].hasAttribute('checked');

            if (this.options.type == 'swatch') {
                result = element.hasClass('active');
            }

            return result
        },

        /**
         * Check if element enabled
         *
         * @param {Object} element
         * @returns {boolean}
         */
        _isEnabled: function (element) {
            var result = !element[0].hasAttribute('disabled');

            if (this.options.type == 'swatch') {
                result = !element.hasClass('disabled');
            }

            return result;
        },

        /**
         * Get input element
         *
         * @param {Object} element
         * @returns {Object}
         */
        _getInputElement: function (element) {
            var result = element;

            if (this.options.type == 'swatch') {
                result = element.find('input');
            }

            return result;
        },

        /**
         * Update filter value
         *
         * @param {Object} element
         */
        _updateValue: function (element) {
            var id, name, value;

            if (this._isSelected(element)) {
                id = $(element).attr('id');
                name = this._getInputElement(element).attr('name');
                value = this._getInputElement(element).attr('value');

                if (this.options.multiSelectMode) {
                    filterValue.add(id, name, value, this.options.backendType);
                } else {
                    filterValue.addExclusive(id, name, value, this.options.backendType);
                }
            } else {
                filterValue.remove(element.attr('id'));
            }
        },

        /**
         * Trigger click on item event
         *
         * @param {Object} element
         */
        _triggerClickEvent: function (element) {
            $(this.options.popover).trigger('awLayeredNav:filterItemClick', [element]);
        }
    });

    return $.mage.awLayeredNavFilterItem;
});
