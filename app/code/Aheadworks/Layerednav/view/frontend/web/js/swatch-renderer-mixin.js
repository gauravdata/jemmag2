/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'jquery',
    'underscore',
    './filter/value'
], function ($, _, filterValue) {
    'use strict';

    return function (widget) {
        $.widget('mage.SwatchRenderer', widget, {
            options: {
                filterSelector: '#aw-filter-option-',
                originalOptionId: 'original-option-id',
            },

            /**
             * @inheritdoc
             * */
            _EmulateSelected: function (selectedAttributes) {
                var selectedAttributesFiltered = {},
                    filterValues = filterValue.getPrepared(),
                    attributeCode,
                    optionId;

                if (!_.isEmpty(filterValues)) {
                    $.each(filterValues, $.proxy(function (index, option) {
                        attributeCode = option.key;
                        optionId = $(this.options.filterSelector
                            + attributeCode
                            + '-'
                            + option.value).attr(this.options.originalOptionId);

                        if (!_.isUndefined(optionId)) {
                            selectedAttributesFiltered[attributeCode] = optionId;
                        } else {
                            selectedAttributesFiltered[attributeCode] = option.value;
                        }
                        this._emulateSelectedImproved(selectedAttributesFiltered);
                    }, this));
                } else {
                    $(document).on('awLayeredNav:initSwatchValues', $.proxy(this._onInitSwatchValues, this));
                }
             },

            /**
             * Improved version of emulate mouse click (based on version from 2.3)
             * @param {Object} [selectedAttributes]
             * @private
             */
            _emulateSelectedImproved: function (selectedAttributes) {
                $.each(selectedAttributes, $.proxy(function (attributeCode, optionId) {
                    var elem = this.element.find('.' + this.options.classes.attributeClass +
                        '[attribute-code="' + attributeCode + '"] [option-id="' + optionId + '"]'),
                        parentElem = elem.parent();

                    if (!elem.hasClass('selected')) {
                        if (parentElem.hasClass(this.options.classes.selectClass)) {
                            parentElem.val(optionId);
                            parentElem.trigger('change');
                        } else {
                            elem.trigger('click');
                        }
                    }
                }, this));
            },

            /**
             * @inheritdoc
             * */
            _setPreSelectedGallery: function () {
                this._super();

                var mediaCallData= {'product_id': this.getProduct()},
                    cacheIndex = JSON.stringify(mediaCallData);

                if (_.isEmpty(this.options.mediaCache[cacheIndex])) {
                    delete this.options.mediaCache[cacheIndex];
                }
            },

            /**
             * On init swatch values handler
             *
             * @param {Event} event
             */
            _onInitSwatchValues: function (event) {
                this._EmulateSelected({});
            }
        });

        return $.mage.SwatchRenderer;
    }
});
