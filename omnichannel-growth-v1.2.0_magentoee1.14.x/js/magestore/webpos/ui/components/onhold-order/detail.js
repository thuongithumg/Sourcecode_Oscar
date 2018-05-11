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
        'underscore',
        'jquery',
        'ko',
        'model/sales/order-factory',
        'view/base/abstract',
        'model/sales/order/total',
        'action/cart/checkout',
        'helper/price',
        'model/sales/order/status',
        'mage/translate'
    ],
    function (
        _,
        $,
        ko,
        OrderFactory,
        Component,
        orderTotal,
        Checkout,
        priceHelper,
        orderStatus,
        $t
    ) {
        "use strict";

        return Component.extend({
            orderData: ko.observable(null),
            orderListView: ko.observable(''),
            isEmptyData: ko.observable(false),
            totalValues: ko.observableArray([]),
            canInvoice: ko.observable(true),
            canCancel: ko.observable(true),
            canShip: ko.observable(true),
            canCreditmemo: ko.observable(true),
            canSync: ko.observable(true),
            isOnHold: ko.observable(true),
            defaults: {
                template: 'ui/onhold-order/detail',
                templateTop: 'ui/onhold-order/view/top',
                templateBilling: 'ui/order/view/billing',
                templateShipping: 'ui/order/view/shipping',
                templateTotal: 'ui/order/view/total',
                templateItems: 'ui/order/view/items',
            },
            statusObject: orderStatus.getStatusObjectView(),
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
                if(data) {
                    var items = data.items;
                    $.each(items, function (index, value) {
                        if (value.base_discount_amount < 0 || isNaN(value.base_discount_amount))
                            value.base_discount_amount = 0;
                        if (value.discount_amount < 0 || isNaN(value.discount_amount))
                            value.discount_amount = 0;
                        if (value.tax_amount < 0 || isNaN(value.tax_amount))
                            value.tax_amount = 0;
                        if (value.base_tax_amount < 0 || isNaN(value.base_tax_amount))
                            value.base_tax_amount = 0;
                    });
                }
                this.orderData(data);
                this.orderListView(object);
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
                                self.totalValues.push(
                                    {
                                        totalValue: priceHelper.formatPrice(self.orderData()[totalCode]),
                                        totalLabel: value.totalName == 'base_discount_amount' &&
                                        (self.orderData().discount_description || self.orderData().coupon_code)?
                                        (value.totalLabel)+' ('+(self.orderData().discount_description?
                                            self.orderData().discount_description:self.orderData().coupon_code)+
                                        ')':(value.totalLabel)
                                    }
                                );
                            }
                        }else{
                            if((self.orderData()[value.totalName] && self.orderData()[value.totalName]!=0) || value.required){
                                self.totalValues.push(
                                    {
                                        totalValue: self.convertAndFormatPrice(self.orderData()[value.totalName]),
                                        totalLabel: value.totalName=='base_discount_amount'&&
                                        (self.orderData().discount_description || self.orderData().coupon_code)?
                                        (value.totalLabel)+' ('+(self.orderData().discount_description?
                                            self.orderData().discount_description:self.orderData().coupon_code)+
                                        ')':(value.totalLabel)
                                    }
                                );
                            }
                        }
                    });
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
            getPrice: function (label) {
                if (this.orderData().order_currency_code == window.webposConfig.currentCurrencyCode) {
                    return priceHelper.formatPrice(this.orderData()[label]);
                }
                return this.convertAndFormatPrice(
                    this.orderData()['base_' + label],
                    this.orderData().base_currency_code
                );
            },
            getStatus: function () {
                var self = this;
                return _.find(self.statusObject, function (obj) {
                    return obj.statusClass == self.orderData().status
                }).statusLabel;
            },
            continueProcessing: function(){
                Checkout(this.orderData());
                this.orderData(null);
                this.orderListView()._prepareItems();
            },
            checkValue: function (data) {
                if(data.totalLabel == $t('Gift Card')){
                    data.totalValue = priceHelper.formatPrice(-priceHelper.toNumber(data.totalValue));
                }

                if (priceHelper.toNumber(data.totalValue) == 0 && data.totalLabel == $t('Change')) {
                    return false;
                } else {
                    return true;
                }
            },
            cancelOnhold: function(){
                OrderFactory.get().delete(this.orderData().entity_id);
                this.orderData(null);
                this.orderListView()._prepareItems();
                this.isEmptyData(true);
            }
        });
    }
);