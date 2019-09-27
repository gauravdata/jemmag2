/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/ui-select',
    'mage/translate',
], function ($, _, Element, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            expand: true,
            select: true,
            expandLabel: $t('Expand All'),
            collapseLabel: $t('Collapse All'),
            selectLabel: $t('Select All'),
            clearLabel: $t('Clear All'),
            invertSelectionLabel: $t('Invert Selection'),
            expandLinkLabel: '',
            selectLinkLabel: ''
        },

        /**
         * Initializes UISelect component.
         *
         * @returns {UISelect} Chainable.
         */
        initialize: function () {
            this._super();

            this.expandLinkLabel(this.expandLabel);
            this.selectLinkLabel(this.selectLabel);

            return this;
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Abstract} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
                    'expandLinkLabel',
                    'selectLinkLabel'
                ]);

            return this;
        },

        /**
         * Expand all/collapse all toggle
         */
        expandAllToggle: function () {
            this.expand = !this.expand;
            if (this.expand) {
                this.expandLinkLabel(this.expandLabel);
                this.collapseOptions(this.options());
            } else {
                this.expandLinkLabel(this.collapseLabel);
                this.expandOptions(this.options());
            }
        },

        /**
         * Expand all options
         *
         * @param Array options
         */
        expandOptions: function(options) {
            if (!_.isEmpty(options)) {
                options.forEach(function(elem){
                    elem.visible(true);
                    this.expandOptions(elem.optgroup);
                }.bind(this));
            }
        },

        /**
         * Collapse all options
         *
         * @param Array options
         */
        collapseOptions: function(options) {
            if (!_.isEmpty(options)) {
                options.forEach(function(elem){
                    elem.visible(false);
                    this.expandOptions(elem.optgroup);
                }.bind(this));
            }
        },

        /**
         * Select all/clear all toggle
         */
        selectAllToggle: function () {
            this.select = !this.select;
            if (this.select) {
                this.selectLinkLabel(this.selectLabel);
                this.clearOptions(this.options());
            } else {
                this.selectLinkLabel(this.clearLabel);
                this.selectOptions(this.options());
            }
        },

        /**
         * Select all options
         *
         * @param Array options
         */
        selectOptions: function(options) {
            if (!_.isEmpty(options)) {
                options.forEach(function(elem){
                    if (!_.contains(this.value(), elem.value)) {
                        this.value.push(elem.value);
                    }
                    this.selectOptions(elem.optgroup);
                }.bind(this));
            }
        },

        /**
         * Clear all options
         *
         * @param Array options
         */
        clearOptions: function(options) {
            if (!_.isEmpty(options)) {
                options.forEach(function(elem){
                    this.value(_.without(this.value(), elem.value));
                    this.clearOptions(elem.optgroup);
                }.bind(this));
            }
        },

        /**
         * Invert selection
         */
        invertSelection: function () {
            this.invertOptions(this.options());
        },

        /**
         * Invert options
         *
         * @param Array options
         */
        invertOptions: function (options) {
            if (!_.isEmpty(options)) {
                options.forEach(function(elem){
                    if (!_.contains(this.value(), elem.value)) {
                        this.value.push(elem.value);
                    } else {
                        this.value(_.without(this.value(), elem.value));
                    }
                    this.invertOptions(elem.optgroup);
                }.bind(this));
            }
        }
    });
});
