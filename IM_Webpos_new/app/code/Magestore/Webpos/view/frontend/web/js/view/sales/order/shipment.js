/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/sales/order-factory',
        'mage/translate',
        'Magestore_Webpos/js/view/sales/order/action',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/action/sales/order/shipment/create',
        'Magento_Ui/js/modal/confirm',
        
    ],
    function ($, ko, OrderFactory, $t, Component, eventmanager, createShipmentAction, Confirm) {
        "use strict";

        return Component.extend({
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),
            formId: 'shipment-popup-form',
            submitArray: [],
            submitData: {
                "entity":{
                    "orderId": 0,
                    "emailSent": 0,
                    "items": [],
                    "tracks": [],
                    "comments": []
                }
            },
            items: {},
            comment: {},
            track: {},

            defaults: {
                template: 'Magestore_Webpos/sales/order/shipment',
            },

            initialize: function () {
                var self = this;
                this._super();
                eventmanager.observer('sales_order_shipment_afterSave', function(event, data){
                    if(data.response && data.response.entity_id>0){
                        var deferedSave = $.Deferred();
                        OrderFactory.get().setData(data.response).setMode('offline').save(deferedSave);
                        self.parentView().updateOrderListData(data.response);
                    }
                });
            },

            validateQty: function(data, event){
                var qty = event.target.value;
                var maxQty = data.qty_ordered - data.qty_shipped - data.qty_refunded - data.qty_canceled;
                if(qty=='')
                    qty = maxQty;
                if(isNaN(qty) || parseFloat(qty)<0 || parseFloat(qty)>maxQty)
                    qty = maxQty;
                event.target.value = qty;
            },
            
            submit: function(){
                var self = this;
                Confirm({
                    content: $t('Are you sure you want to ship this order?'),
                    actions: {
                        confirm: function () {
                            self.submitArray = $('#'+self.formId).serializeArray();
                            createShipmentAction.execute(self.submitArray, self.orderData(), $.Deferred(), self);
                        },
                        always: function (event) {
                            event.stopImmediatePropagation();
                        }
                    }
                });
            }
        });
    }
);