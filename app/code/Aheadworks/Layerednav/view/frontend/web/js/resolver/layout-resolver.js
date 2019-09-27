/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
        'jquery'
    ], function ($) {
        'use strict';

        var oneColumnClass = 'page-layout-1column',
            twoColumnsRightClass = 'page-layout-2columns-right';

        return {

            /**
             * Check is one column layout
             *
             * @returns {bool}
             */
            isOneColumnLayout: function () {
                return $('body').hasClass(oneColumnClass);
            },

            /**
             * Check is two columns-right layout
             *
             * @returns {bool}
             */
            isTwoColumnsRightLayout: function () {
                return $('body').hasClass(twoColumnsRightClass);
            }
        };
    }
);
