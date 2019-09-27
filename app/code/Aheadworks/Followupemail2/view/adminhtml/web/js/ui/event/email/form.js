/**
* Copyright 2019 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'Magento_Ui/js/form/form'
], function ($, Form) {
    'use strict';

    return Form.extend({
        /**
         * Validate and save form with sendtest parameter (content A).
         */
        sendtestA: function () {
            var data = {
                "sendtest": 1,
                "content_id": 1,
            }
            this.save(false, data);
        },

        /**
         * Validate and save form with sendtest parameter (content B).
         */
        sendtestB: function () {
            var data = {
                "sendtest": 1,
                "content_id": 2,
            }
            this.save(false, data);
        },

        /**
         * Submits form
         *
         * @param {String} redirect
         */
        submit: function (redirect) {
            this._super(redirect);
            this.source.set('data.sendtest', 0);
        },
    })
});