/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    './resolver/layout-resolver',
    'jquery/jquery-storageapi'
], function ($, _, layoutResolver) {
    'use strict';

    $.widget('mage.awLayeredNavCollapse', {
        storage: null,
        storageName: 'aw-ln-collapse',
        slideContentSelector: '.filter-options-content',
        slideDelay: 200,
        showMoreClass: 'show-more',
        showMoreSelector: '[id^=aw-layered-nav-collapse-show-]',
        showLessSelector: '[id^=aw-layered-nav-collapse-hide-]',
        activeFilterSelector: '.filter-options-item.active',

        /**
         * Initialize widget
         */
        _create: function () {
            this.storage = $.sessionStorage;
            this._init();
            this._bind();
        },

        /**
         * Init state
         */
        _init: function() {
            var savedState = this._getSavedState();

            if (savedState.hasOwnProperty('expanded') && !layoutResolver.isOneColumnLayout()) {
                if (savedState.expanded) {
                    $(this.element).addClass('active');
                } else {
                    $(this.element).removeClass('active');
                }
            }
            if (savedState.less) {
                this._enableLessVisibility();
            }
            this._toggleContent($(this.element).hasClass('active'));
        },

        /**
         * Event binding
         */
        _bind: function () {
            this._on({
                /**
                 * Calls callback when event is triggered
                 */
                'click [data-role=title]': function () {
                    if (layoutResolver.isOneColumnLayout() && !$(this.element).hasClass('active')) {
                        this._collapseOtherFilters();
                    }
                    $(this.element).toggleClass('active');
                    this._toggleContent($(this.element).hasClass('active'));
                    this._saveState();
                },

                'click [id^=aw-layered-nav-collapse-show-]': function () {
                    this._toggleShowMoreVisibility();
                    this._saveState();
                }.bind(this),

                'click [id^=aw-layered-nav-collapse-hide-]': function () {
                    var srcHeight = $(this.element).height();

                    this._toggleShowMoreVisibility();
                    this._restorePosition(srcHeight);
                    this._saveState();
                }.bind(this),
            });
        },

        /**
         * Restore screen position
         *
         * @param {number} previousHeight
         */
        _restorePosition: function(previousHeight) {
            var dstHeight = $(this.element).height(),
                srcViewPos = $('body, html').scrollTop(),
                dstViewPos = srcViewPos - (previousHeight - dstHeight);

            $('body, html').animate({scrollTop: dstViewPos}, this.slideDelay);
        },

        /**
         * Toggle content dropdown
         *
         * @param {boolean} switcher
         */
        _toggleContent: function (switcher) {
            if (switcher) {
                $(this.element).children(this.slideContentSelector).slideDown(this.slideDelay);
            } else {
                $(this.element).children(this.slideContentSelector).slideUp(this.slideDelay);
            }
        },

        /**
         * Toggle visibility of filter items
         */
        _toggleShowMoreVisibility: function () {
            var options = $(this.element).find('.item');

            options.each(function(index, selector) {
                if ($(selector).hasClass('show') || $(selector).hasClass('hide')) {
                    $(selector).toggleClass('show hide');
                } else if ($(selector).hasClass(this.showMoreClass)) {
                    this._toggleShowMoreLink(selector);
                } else if ($(selector).hasClass('shaded') || $(selector).hasClass('no-shaded')) {
                    $(selector).toggleClass('shaded no-shaded');
                }
            }.bind(this));
        },

        /**
         * Toggle show-more link
         * @param {string} selector
         */
        _toggleShowMoreLink: function(selector) {
            $(selector).children(this.showMoreSelector).toggleClass('show hide');
            $(selector).children(this.showLessSelector).toggleClass('show hide');
        },

        /**
         * Toggle visibility of filter items
         */
        _enableLessVisibility: function () {
            var options = $(this.element).find('.item');

            options.each(function(index, selector) {
                if ($(selector).hasClass('hide')) {
                    $(selector).toggleClass('show hide');
                } else if ($(selector).hasClass(this.showMoreClass)) {
                    $(selector).children(this.showMoreSelector).addClass('hide').removeClass('show');
                    $(selector).children(this.showLessSelector).addClass('show').removeClass('hide');
                } else if ($(selector).hasClass('shaded')) {
                    $(selector).toggleClass('shaded no-shaded');
                }
            }.bind(this));
        },

        /**
         * Get current state
         *
         * @returns {Object}
         */
        _getCurrentState: function() {
            return {
              "expanded":$(this.element).hasClass('active'),
              "less":$(this.element).find(this.showLessSelector).hasClass('show'),
            };
        },

        /**
         * Save state
         */
        _saveState: function() {
            var savedParams,
                elemId = this.element[0].id;

            if (!this.storage.isEmpty(this.storageName)) {
                savedParams = this.storage.get(this.storageName);
            } else {
                savedParams = {};
            }

            savedParams[elemId] = this._getCurrentState();
            this.storage.set(this.storageName, savedParams);
        },

        /**
         * Get saved state
         *
         * @returns {Object}
         */
        _getSavedState: function() {
            var savedParams,
                elemId = this.element[0].id;

            if (!this.storage.isEmpty(this.storageName)) {
                savedParams = this.storage.get(this.storageName);

                if (savedParams.hasOwnProperty(elemId)) {
                    return savedParams[elemId];
                }
            }
            return {};
        },

        /**
         * Collapse other filters
         */
        _collapseOtherFilters: function () {
            $(this.activeFilterSelector).each(function (index, filter) {
                _.delay(function() {
                    $(filter).removeClass('active');
                }, this.slideDelay);
                $(filter).children(this.slideContentSelector).slideUp(this.slideDelay);
            }.bind(this))
        }
    });

    return $.mage.awLayeredNavCollapse;
});
