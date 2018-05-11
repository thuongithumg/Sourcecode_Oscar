/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'jquery',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/customer/complain',
        'Magestore_Webpos/js/model/customer/credit-history',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/model/customer/group-factory',        
        'Magestore_Webpos/js/helper/datetime',
        'Magestore_Webpos/js/region-updater'
        
    ],
    function($, ViewManager, complainModel, creditHistory, OrderFactory, CustomerFactory, CustomerGroupFactory, dateHelper) {
        'use strict';
        return function (data) {
            CustomerFactory.get().setSelectedCustomer(data.id);
            
            /* Region Updater For Add Address*/
            var addressForm = $('#form-customer-add-address');
            var countryList = addressForm.find('.country_id');
            var regionList = addressForm.find('.region_id');
            var regionInput = addressForm.find('.region');
            countryList.regionUpdater({
                regionList: regionList,
                regionInput: regionInput,
                regionJson: JSON.parse(window.webposConfig.regionJson)
            });

            /* Set Data For Group*/
            var groupDeferred = CustomerGroupFactory.get().load(data.group_id);
            groupDeferred.done(function(response){
                if (response) {
                    data.group_label = response.code;
                }
                ViewManager.getSingleton('view/customer/customer-view').setData(data);
            });
            ViewManager.getSingleton('view/customer/customer-view').setData(data);

            /* Set Data For Address*/
            ViewManager.getSingleton('view/customer/customer-view').setAddressData(data.addresses);

            var daysPeriod = window.webposConfig.order_sync_time_period;
  
            var result = new Date();
            result.setDate(result.getDate() - daysPeriod);
            var dateString = dateHelper.formatDate(result);
            
            /* Load Customer Order*/
            var deferred = $.Deferred();
            OrderFactory.get().getCollection().reset()
                .addFieldToFilter('customer_id', String(data.id), 'eq')
                .addFieldToFilter('created_at', dateString, 'gt')
                .setOrder('increment_id','DESC')
                .load(deferred);
            deferred.done(function(response){
                var allOrder = response.items;
                ViewManager.getSingleton('view/customer/customer-view').setOrderData(response.items);
                var totalSale = 0;
                $.each(allOrder, function (index, value) {
                    totalSale = totalSale + value.base_grand_total;
                    if (value.base_total_refunded > 0) {
                        var orderHistory = value.status_histories;
                        $.each(orderHistory, function (index, history) {
                            if (history.entity_name == 'creditmemo') {
                                value.created_at = history.created_at;
                            }
                        });
                    }
                });

                ViewManager.getSingleton('view/customer/customer-view').setTotalSale(totalSale);
            });

            var refund = OrderFactory.get().getCollection().reset()
                .addFieldToFilter('customer_id', String(data.id), 'eq')
                .setOrder('increment_id','DESC')
                .load();
            refund.done(function (response) {
                var allOrder = response.items;
                var orderRefund = [];
                $.each(allOrder, function (index, value) {
                    if (value.base_total_refunded > 0) {
                        var orderHistory = value.status_histories;
                        $.each(orderHistory, function (index, history) {
                            if (history.entity_name == 'creditmemo' && history.created_at > dateString) {
                                value.created_at = history.created_at;
                            }
                        });
                        orderRefund.push(value);
                    }
                });
                ViewManager.getSingleton('view/customer/customer-view').setRefundData(orderRefund);
            });


            /* Load Customer Complain*/
            var deferredComplain = $.Deferred();
            complainModel().getCollection()
                .addFieldToFilter('customer_email', String(data.email), 'eq')
                .load(deferredComplain);
            deferredComplain.done(function(response){
                ViewManager.getSingleton('view/customer/customer-view').setCustomerComplain(response.items);
            });

            /* Load Customer Credit*/
            var deferredCredit = $.Deferred();
            creditHistory().get().setMode('online').getCollection().addFieldToFilter('customer_id', data.id, 'eq')
                .load(deferredCredit);
            deferredCredit.done(function(response){
                ViewManager.getSingleton('view/customer/customer-view').setCreditHistory(response.items);
            });
        }
    }
);
