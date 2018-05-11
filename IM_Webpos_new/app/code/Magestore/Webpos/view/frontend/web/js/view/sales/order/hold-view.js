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
        'Magestore_Webpos/js/view/base/abstract',
        'Magestore_Webpos/js/model/sales/order/total',
        'Magestore_Webpos/js/action/cart/checkout',
        'Magestore_Webpos/js/action/cart/cancel-onhold',
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/checkout',
    ],
    function (
        $, 
        ko,
        OrderFactory,
        $t,
        Component,
        orderTotal,
        Checkout,
        CancelOnhold,
        priceHelper,
        Helper,
        CheckoutModel
    ) {
        "use strict";

        return Component.extend({
            orderData: ko.observable(null),
            orderListView: ko.observable(''),
            isShowActionPopup: ko.observable(false),
            isEmptyData: ko.observable(false),
            totalValues: ko.observableArray([]),
            canInvoice: ko.observable(true),
            canCancel: ko.observable(true),
            canShip: ko.observable(true),
            canCreditmemo: ko.observable(true),
            canSync: ko.observable(true),
            isOnHold: ko.observable(true),
            defaults: {
                template: 'Magestore_Webpos/sales/order/hold-view',
                templateTop: 'Magestore_Webpos/sales/order/hold/top',
                templateBilling: 'Magestore_Webpos/sales/order/hold/billing',
                templateShipping: 'Magestore_Webpos/sales/order/hold/shipping',
                templateShippingMethod: 'Magestore_Webpos/sales/order/hold/shipping-method',
                templatePaymentMethod: 'Magestore_Webpos/sales/order/hold/payment-method',
                templateTotal: 'Magestore_Webpos/sales/order/hold/total',
                templateItems: 'Magestore_Webpos/sales/order/hold/items',
                templateComments: 'Magestore_Webpos/sales/order/view/comments',
            },

            initialize: function () {
                this._super();
                var self = this;
                ko.computed(function() {
                    return self.orderData();
                }).subscribe(function() {
                    if(self.orderData()){
                        self.canInvoice(OrderFactory.get().setData(self.orderData.call()).canInvoice());
                        self.canCancel(OrderFactory.get().setData(self.orderData.call()).canCancel());
                        self.canShip(OrderFactory.get().setData(self.orderData.call()).canShip());
                        self.canCreditmemo(OrderFactory.get().setData(self.orderData.call()).canCreditmemo());
                        self.canSync(OrderFactory.get().setData(self.orderData.call()).canSync());
                        self.isOnHold(OrderFactory.get().setData(self.orderData.call()).isOnHold());
                    }
                });
                
                self.isNotOnHold = ko.pureComputed(function(){
                    return (self.isOnHold())?false:true;
                });
            },
            
            setData: function(data, object){
                this.orderData(data);
                this.orderListView(object);
                this.isShowActionPopup(false);
                this.isEmptyData(false);
                var self = this;
                this.totalValues([]);
                var totalArray = orderTotal.getTotalOrderHold();
                if(data)
                    $.each(totalArray, function(index, value){
                        var order_currency_code = self.orderData().order_currency_code;
                        var current_currency_code = window.webposConfig.currentCurrencyCode;
                        if(
                            (order_currency_code == current_currency_code) &&
                            (value.totalName == 'base_total_paid' || value.totalName == 'webpos_base_change'
                            || value.totalName == 'base_discount_amount' || value.totalName == 'base_grand_total'
                            || value.totalName == 'base_subtotal'|| value.totalName == 'base_tax_amount'
                            || value.totalName == 'base_shipping_amount')
                        ){
                            if((self.orderData()[value.totalName] && self.orderData()[value.totalName]!=0) || value.required){
                                var totalCode = value.totalName.replace("base_", "");
                                if(totalCode == 'subtotal_incl_tax' && window.webposConfig['tax/sales_display/subtotal'] && window.webposConfig['tax/sales_display/subtotal'] == '1'){
                                    totalCode = 'subtotal';
                                }
                                self.totalValues.push(
                                    {
                                        totalValue: priceHelper.formatPrice(self.orderData()[totalCode]),
                                        totalLabel: value.totalName == 'base_discount_amount' &&
                                        (self.orderData().discount_description || self.orderData().coupon_code)?
                                        $t(value.totalLabel)+' ('+(self.orderData().discount_description?
                                            self.orderData().discount_description:self.orderData().coupon_code)+
                                        ')':$t(value.totalLabel)
                                    }
                                );
                            }
                        }else{
                            if((self.orderData()[value.totalName] && self.orderData()[value.totalName]!=0) || value.required){
                                var totalCode = value.totalName;
                                if(totalCode == 'base_subtotal_incl_tax' && window.webposConfig['tax/sales_display/subtotal'] && window.webposConfig['tax/sales_display/subtotal'] == '1'){
                                    totalCode = 'base_subtotal';
                                }
                                self.totalValues.push(
                                    {
                                        totalValue: self.convertAndFormatPrice(self.orderData()[totalCode]),
                                        totalLabel: totalCode=='base_discount_amount'&&
                                        (self.orderData().discount_description || self.orderData().coupon_code)?
                                        $t(value.totalLabel)+' ('+(self.orderData().discount_description?
                                            self.orderData().discount_description:self.orderData().coupon_code)+
                                        ')':$t(value.totalLabel)
                                    }
                                );
                            }
                        }
                    });
            },

            showActionPopup: function(data){
                if(this.orderViewObject.isShowActionPopup.call())
                    this.orderViewObject.isShowActionPopup(false);
                else
                    this.orderViewObject.isShowActionPopup(true);
            },

            showPopup: function(type){
                this.isShowActionPopup(false);
                this.popupArray[type].display(true);
            }, 

            getAddressType: function(type){
                switch (type) {
                    case 'billing':
                        return this.orderData.call().billing_address;
                        break;
                    case 'shipping':
                        return this.orderData.call().extension_attributes.shipping_assignments[0].shipping.address;
                        break;
                }
            },

            convertAndFormatPrice: function(price, from, to){
                return priceHelper.convertAndFormat(price, from, to);
            },

            getCustomerName: function(type) {
                var address = this.getAddressType(type);                
                return address.firstname + ' ' + address.lastname;
            },

            getAddress: function(type){
                var address = this.getAddressType(type);
                var city = address.city ? address.city + ', ': '';
                var region = address.region ? address.region + ', ' : '';
                var postcode = address.postcode ? address.postcode + ', ' : '';
                return city + region + postcode + address.country_id;
            },
            
            getJsObject: function(){
                return {
                    orderView: this,
                    orderListView: this.orderListView.call(),
                }
            },
            
            continueProcessing: function(){
                Checkout(this.orderData());
                this.orderData(null);
                this.orderListView()._prepareItems();
            },
            applyCartDiscountAfterCheckoutHoldOrder : function(){
                return CheckoutModel._applyCartDiscountOnline();
            },
            
            cancelOnhold: function(){
                CancelOnhold(this.orderData());
                this.orderData(null);
                this.orderListView()._prepareItems();
                this.isEmptyData(true);
            },
            canShowComment: function () {
                var canShowComment = false;
                if (this.orderData() && this.orderData().status_histories) {
                    $.each(this.orderData().status_histories, function (index, value) {
                        if (value.comment && value.comment != '') canShowComment = true;
                    });
                }
                return canShowComment;
            },
            getItemPriceFormated: function(item){
                var self = this;
                var formated = ''
                if(item){
                    var displayIncludeTax = Helper.isCartDisplayIncludeTax('price');
                    var displayIncludeTax = false;
                    if(window.webposConfig.currentCurrencyCode == self.orderData().order_currency_code){
                        formated = (displayIncludeTax)?item.price_incl_tax:item.price;
                    }else{
                        if(window.webposConfig.currentCurrencyCode == self.orderData().base_currency_code){
                            formated = (displayIncludeTax)?item.base_price_incl_tax:item.base_price;
                        } else{
                            formated = (displayIncludeTax)?item.base_price_incl_tax:item.base_price;
                            formated = priceHelper.currencyConvert(formated,self.orderData().base_currency_code, window.webposConfig.currentCurrencyCode);
                        }
                    }
                }
                return priceHelper.formatPrice(formated);
            }
        });
    }
);