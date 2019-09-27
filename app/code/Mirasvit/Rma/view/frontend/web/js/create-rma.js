define([
    'underscore',
    'ko',
    'uiComponent',
    'jquery'
], function (_, ko, Component, $) {
    'use strict';
    
    window.offlineOrderNumber = 0;
    window.offlineItemNumber = 0;
    
    return Component.extend({
        showRmaAdditions: ko.observable(0),
        isAllowedToAddOrder: ko.observable(1),
        orderSelectorTitle: ko.observable($.mage.__('Please, select an order')),

        isAllowedOfflineOrder: 0,
        containerSelector: '.ui-mst-rma__create-rma',
        rmaOrderContainerSelector: '.ui-rma-order-container',
        addItemButtonContainerSelector: '.ui-add-item-button-container',
        removeItemButtonContainerSelector: '.ui-remove-item-button-container',
        
        defaults: {
            template:                'Mirasvit_Rma/create-rma',
            OrderTemplateUrl:        '',
            OfflineOrderTemplateUrl: '',
            isAllowedOfflineOrder:   false,
            isAllowedMulitpleOrders: false,
            allowedOrder:            []
        },
        
        initialize: function () {
            this._super();
            this._bind();
            
            return this;
        },
        
        _bind: function () {
            var self = this;
            var body = $('body');

            self.isAllowedOfflineOrder = parseInt(self.isAllowedOfflineOrder);
            body.on('click', '.mst-rma-create__order .remove', function () {
                var el = $(this).closest('.rma-step2');
                el.remove();
                if (!$('.ui-rma-order-container > div').length) {
                    self.orderSelectorTitle($.mage.__('Please, select an order'));
                    self.showRmaAdditions(0);
                    if (self.isAllowedMulitpleOrders == 0) {
                        self.isAllowedToAddOrder(1);
                    }
                }
            });
            
            body.on('click', this.addItemButtonContainerSelector, function () {
                var parent = $(this).closest('.ui-offline-order-container');
                var orderNumber = $('.ui-receiptnumber', parent).data('order-number');
                var html = $('#item_returnreasons').html().replace(/%%item_id%%/g, window.offlineItemNumber)
                    .replace(/%%order_id%%/g, orderNumber);
                $('.ui-offline-items-container', parent).append(html);
                window.offlineItemNumber++;
            });
            
            body.on('click', this.removeItemButtonContainerSelector, function () {
                $(this).closest('.rma-one-item').remove();
            });
            
        },
        
        addStoreOrder: function () {
            this.orderSelectorTitle($.mage.__('Add another order'));
            this.showRmaAdditions(1);
        },
        
        addOfflineOrder: function () {
            if (!this.isAllowedToAddOrder()) {
                return;
            }
            this.loader(true);

            var self = this;
            $.ajax({
                url:      this.OfflineOrderTemplateUrl,
                type:     'POST',
                dataType: 'json',
                complete: function (data) {
                    self.loader(false);
                    data = data.responseJSON;
                    if (data.error) {
                        alert(data.error);
                    } else {
                        window.offlineOrderNumber++;
                        
                        var html = data.blockHtml.replace(/%%order_id%%/g, window.offlineOrderNumber)
                            .replace(/%%item_id%%/g, window.offlineItemNumber);
                        
                        $(self.rmaOrderContainerSelector).append(html);

                        var el = $('.ui-offline-order-container', self.rmaOrderContainerSelector).last();
                        $(self.addItemButtonContainerSelector, el).click();

                        self.orderSelectorTitle($.mage.__('Add another order'));
                        self.showRmaAdditions(1);
    
                        if (self.isAllowedMulitpleOrders == 0) {
                            self.isAllowedToAddOrder(0);
                        }
                    }
                }.bind(this)
            });
        },
        
        addSelectedStoreOrder: function () {
            if (!this.isAllowedToAddOrder()) {
                return;
            }
            var orderId = $('#selected_order_id').val();
            var orderSelector = '.rma-order-id-' + orderId;
            
            if (orderId > 0) {
                if (!$(orderSelector).length) {
                    this.loader(true);
                    
                    var data = {"order_id": orderId};
                    var self = this;
                    $.ajax({
                        url:      this.OrderTemplateUrl,
                        type:     'POST',
                        dataType: 'json',
                        data:     data,
                        complete: function (data) {
                            self.loader(false);
                            
                            data = data.responseJSON;
                            
                            if (data.error) {
                                alert(data.error);
                            } else {
                                $(self.rmaOrderContainerSelector).append(data.blockHtml);
                            }

                            self.orderSelectorTitle($.mage.__('Add another order'));
                            self.showRmaAdditions(1);
                            if (self.isAllowedMulitpleOrders == 0) {
                                self.isAllowedToAddOrder(0);
                            }
                        }.bind(this)
                    });
                } else {
                    alert($.mage.__('Order already exists in current RMA'));
                }
            } else {
                alert($.mage.__('Select Order'));
            }
        },
        
        loader: function (show) {
            if (show) {
                $(this.containerSelector).trigger('processStart');
            } else {
                $(this.containerSelector).trigger('processStop');
            }
        }
    });
});
