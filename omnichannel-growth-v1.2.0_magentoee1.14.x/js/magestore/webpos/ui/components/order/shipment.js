/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define(
    [
        'jquery',
        'ko',
        'model/sales/order-factory',
        'mage/translate',
        'ui/components/order/action',
        'eventManager',
        'action/sales/order/shipment/create',
        'ui/lib/modal/confirm',
        'helper/general'
    ],
    function ($, ko, OrderFactory, $t, Component, eventmanager, createShipmentAction, Confirm, Helper) {
        "use strict";

        return Component.extend({
            isUsingInventorySuccess: Helper.isInventorySuccessEnable(),
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
                template: 'ui/order/shipment',
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
            },

            getWarehouseName: function(warehouseId){
                var self = this;
                var warehouses = Helper.getPluginConfig('os_inventory_success', 'warehouses');
                var warehouseName = '';
                if(warehouses && warehouses.length > 0){
                    $.each(warehouses, function(index, warehouse){
                        if(warehouse.warehouse_id == warehouseId){
                            warehouseName = warehouse.warehouse_name;
                        }
                    });
                }
                return warehouseName;
            }
        });
    }
);