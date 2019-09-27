/**
 * Copyright 2019 aheadWorks. All rights reserved.\nSee LICENSE.txt for license details.
 */

define([
    'ko',
    'underscore',
    'Magento_Ui/js/grid/tree-massactions'
], function (ko, _, TreeMassactions) {
    'use strict';

    return TreeMassactions.extend({
        /**
         * Recursive initializes observable actions.
         *
         * @param {Array} actions
         * @param {String} [prefix]
         * @returns {TreeMassactions} Chainable.
         */
        recursiveObserveActions: function (actions, prefix) {
            _.each(actions, function (action) {
                if (prefix) {
                    action.type = prefix + '.' + action.type;
                }

                if (action.actions) {
                    action.visible = ko.observable(false);
                    action.parent = actions;
                    this.recursiveObserveActions(action.actions, action.type);
                }
            }, this);

            return this;
        },
    });
});
