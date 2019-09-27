/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'Magento_Ui/js/modal/modal-component'
], function ($, ModalComponent) {
    'use strict';

    return ModalComponent.extend({
        /**
         * Sets title for modal
         *
         * @param {String} title
         */
        setTitle: function (title) {
            if (this.title !== title) {
                this.title = title;
            }

            if (this.modal) {
                var titleSelector = this.rootSelector + ' [data-role="title"]';
                var $title = $(titleSelector);
                $title.text(title);
            }
        },
    });
});
