/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'require',
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/view/base/list/collection-list',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/view/checkout/checkout/payment_selected',
        'Magestore_Webpos/js/model/checkout/payment-factory',
        'Magestore_Webpos/js/view/checkout/checkout/renderer/payment-factory',
    ],
    function (require, $, ko, ViewManager, colGrid, Helper, SelectedPayment, PaymentFactory, RenderPaymentFactory) {
        "use strict";
        return colGrid.extend({
            items: ko.observableArray([]),
            columns: ko.observableArray([]),
            onlineItems: ko.observableArray([]),
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/payment',
            },
            initialize: function () {
                if(!this.needinitObserver){
                    this.initObserver();
                }
                this.isShowHeader = true;
                this.model = PaymentFactory.get().setMode('offline');
                this._super();
                this._render();
            },
            initObserver: function(){
                var self = this;
                self.needinitObserver = true;
                Helper.observerEvent('go_to_checkout_page', function(){
                    self.saveDefaultPaymentMethod();
                });
                Helper.observerEvent('save_default_payment', function(){
                    self.saveDefaultPaymentMethod();
                });
                Helper.observerEvent('reset_payments_data', function(){
                    self.collection.reset();
                    SelectedPayment().renewPayments();
                });
                Helper.observerEvent('load_payment_online_after', function(event, data){
                    if(data && data.items){
                        for (var i in data.items) {
                            data.items[i].columns = self.columns;
                        }
                        self.onlineItems(data.items);
                        self._prepareItems();
                        self.saveDefaultPaymentMethod();
                    }
                });
            },
            _prepareCollection: function () {
                this.filterAttribute = 'code';
                if(this.collection == null) {
                    this.collection = this.model.getCollection();
                }
            },
            _prepareItems: function () {
                var self = this;
                if (Helper.isUseOnline('checkout')) {
                    self.items(self.onlineItems());
                }else{
                    var deferred = self.getCollection().load();
                    self.startLoading();
                    deferred.done(function (data) {
                        self.setItems(data.items);
                    });
                    deferred.always(function (error) {
                        self.finishLoading();
                    });
                }
            },
            _prepareColumns: function () {
                this.addColumn({headerText: "Title", rowText: "title", renderer: RenderPaymentFactory.get()});
            },
            setPaymentMethod: function (data, event) {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                if($('#payment_selected') !== undefined){
                    $('#payment_selected').show();
                }
                if(data.type != '1'){
                    viewManager.getSingleton('view/checkout/checkout/payment_selected').addPayment(data);
                }if(data.type == '1'){
                    viewManager.getSingleton('view/checkout/checkout/payment_creditcard').addPayment(data);
                    if($('#payment_creditcard') !== undefined){
                        $('#payment_creditcard').show();
                        viewManager.getSingleton('view/checkout/checkout/swipe/jquery.cardswipe').initSwipe('webpos');
                    }
                }
                if($('#payment_list') !== undefined){
                    $('#payment_list').hide();
                }
                viewManager.getSingleton('view/checkout/checkout/payment_popup')._prepareItems();
            },
            addExtensionMethod: function (data) {
                var viewManager = require('Magestore_Webpos/js/view/layout');
                viewManager.getSingleton('view/checkout/checkout/payment_selected').addExtensionPayment(data);
                if($('#payment_selected') !== undefined){
                    $('#payment_selected').show();
                }
                if($('#payment_creditcard') !== undefined){
                    $('#payment_creditcard').hide();
                }
                if($('#payment_list') !== undefined){
                    $('#payment_list').hide();
                }
                viewManager.getSingleton('view/checkout/checkout/payment_popup')._prepareItems();
            },
            getDefaultPaymentMethod: function () {
                var paymentList = this.items();
                if (Helper.isUseOnline('checkout')) {
                    paymentList = this.onlineItems();
                }
                if(paymentList.length > 0){
                    for(var i = 0; i < paymentList.length; i++){
                        if(paymentList[i].is_default == '1'){
                            return paymentList[i];
                        }
                    }
                }
                return false;
            },
            saveDefaultPaymentMethod: function () {
                this.renewPayments();
                if(this.getDefaultPaymentMethod()){
                    this.setPaymentMethod(this.getDefaultPaymentMethod());
                }
            },
            renewPayments: function () {
                SelectedPayment().renewPayments();
            }
        });
    }
);
