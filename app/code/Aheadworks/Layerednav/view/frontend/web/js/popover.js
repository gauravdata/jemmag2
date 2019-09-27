/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    './filter/value',
    './filter/item/current',
    './url',
    './filter/data/source/ajax',
    './filter/request/bridge',
    './updater',
    './resolver/layout-resolver'
], function ($, filterValue, currentFilterItem, url, dataSource, requestBridge, updater, layoutResolver) {
    'use strict';

    $.widget('mage.awLayeredNavPopover', {
        options: {
            buttonShow: '[data-role=show-button]',
            itemsCountContainer: '[data-role=items-count-container]',
            filtersContainer: '[data-role=filters-container]',
            isPopoverDisabled: false,
            hasActiveFilters: false,
            actionsBlock: '[data-role=aw-layered-nav-actions]',
            overlayBlock: '.aw-layered-nav-overlay'
        },

        /**
         * Initialize widget
         */
        _create: function () {
            this._hidePopover();
            this._bind();
            if (filterValue.getPrepared().length && currentFilterItem.get()) {
                this._updatePopoverData(filterValue.getPrepared());
                this._moveToFilterItem(currentFilterItem.get());
            }
        },

        /**
         * Hide popover and its overlay
         * @private
         */
        _hidePopover: function() {
            this.element.hide();
            $(this.options.overlayBlock).hide();
        },

        /**
         * Show popover and its overlay
         * @private
         */
        _showPopover: function() {
            this.element.show();
            $(this.options.overlayBlock).show();
        },

        /**
         * Event binding
         */
        _bind: function () {
            this.element.on('awLayeredNav:filterValueChange', $.proxy(this._onFilterValueChange, this));
            this.element.on('awLayeredNav:filterItemClick', $.proxy(this._onFilterItemClick, this));
            $(window).on('resize', $.proxy(this._onWindowResize, this));
            $(this.options.overlayBlock).on('click', $.proxy(this._onOverlayClick, this));
        },

        /**
         * On overlay click event handler
         */
        _onOverlayClick: function() {
            this._hidePopover();
        },

        /**
         * On filter value change event handler
         *
         * @param {Event} event
         * @param {Array} filterValue
         */
        _onFilterValueChange: function (event, filterValue) {
            if (currentFilterItem.get()) {
                if (this.options.isPopoverDisabled) {
                    var submitUrl = url.getSubmitUrl(filterValue);

                    requestBridge.submit(submitUrl).then(
                        /**
                         * Called after request finishes
                         */
                        function () {
                            updater.updateAndScrollUpToTop(submitUrl, requestBridge.getResult());
                        }
                    );
                } else {
                    this._updatePopoverData(filterValue);
                }
            }
        },

        /**
         * On filter item click event handler
         *
         * @param {Event} event
         * @param {Object} filterItem
         */
        _onFilterItemClick: function (event, filterItem) {
            if (!this.options.isPopoverDisabled) {
                this._moveToFilterItem(filterItem);
            }
        },

        /**
         * On window resize event handler
         */
        _onWindowResize: function () {
            if (currentFilterItem.get()) {
                this._moveToFilterItem(currentFilterItem.get());
            }
        },

        /**
         * Update popover data
         *
         * @param {Array} filterValue
         */
        _updatePopoverData: function (filterValue) {
            var self = this,
                buttonShow =  this.element.find(this.options.buttonShow);

            this.element.addClass('aw-layered-nav-popover--loading');
            this._disableShowButton();
            dataSource.fetchPopoverData(filterValue).then(
                function () {
                    var popoverData = dataSource.getResult();

                    self.element.find(self.options.itemsCountContainer).html(popoverData.itemsContent);
                    self.element.removeClass('aw-layered-nav-popover--loading');
                    if (popoverData.itemsCount && (filterValue.length || self.options.hasActiveFilters)) {
                        //Clear click binding from previous filter change
                        buttonShow.off('click');
                        buttonShow.one('click', function () {
                            var submitUrl = url.getSubmitUrl(filterValue);

                            requestBridge.submit(submitUrl).then(
                                /**
                                 * Called after request finishes
                                 */
                                function () {
                                    updater.updateAndScrollUpToTop(submitUrl, requestBridge.getResult());
                                }
                            );
                        });
                        self._enableShowButton();
                    }
                }
            );
        },

        /**
         * Disable Show button
         */
        _disableShowButton: function() {
            var buttonShow =  this.element.find(this.options.buttonShow);

            buttonShow.addClass('disabled');
            $(this.options.actionsBlock).trigger('awLayeredNav:showActionDisabled');
        },

        /**
         * Enable Show button
         */
        _enableShowButton: function () {
            var buttonShow =  this.element.find(this.options.buttonShow);

            buttonShow.removeClass('disabled');
            $(this.options.actionsBlock).trigger('awLayeredNav:showActionEnabled');
        },

        /**
         * Move popover to target filter item
         *
         * @param {Object} filterItem
         */
        _moveToFilterItem: function (filterItem) {
            var position = 'left',
                width = $(this.options.filtersContainer).width() + 35,
                top = filterItem.position().top;

            if (layoutResolver.isTwoColumnsRightLayout()) {
                position = 'right';
            }

            if (layoutResolver.isOneColumnLayout()) {
                width = (this.element.parent().width() / 2) - (this.element.width() / 2);
                top = '10';
            }

            this.element.css(position, width + 'px');
            this.element.css('top', top + 'px');
            this._showPopover();
        }
    });

    return $.mage.awLayeredNavPopover;
});
