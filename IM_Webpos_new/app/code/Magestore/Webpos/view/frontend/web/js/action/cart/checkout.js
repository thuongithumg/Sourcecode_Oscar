/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define*/
define(
    [
        'require',
        'jquery',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/cart/discountpopup',
        'Magestore_Webpos/js/action/checkout/select-customer-checkout',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
        'Magestore_Webpos/js/model/customer/customer-factory',
        'Magestore_Webpos/js/action/cart/cancel-onhold',
        'Magestore_Webpos/js/helper/general'
    ],
    function (require, $, CheckoutModel, CartModel, DiscountModel, SelectCustomer, ViewManager, OrderFactory, TotalsFactory, CustomerFactory, CancelOnhold, Helper) {
        'use strict';
        return function (orderData) {
            if(orderData){
                if(orderData.status != "notsync"){
                    var syncOnholdOrder =  Helper.getLocalConfig('os_checkout/sync_order_onhold');
                    if(((orderData.status == 'onhold') || (orderData.status == 'holded')) && syncOnholdOrder == true){
                        CancelOnhold(orderData);
                    }else{
                        OrderFactory.get().delete(orderData.entity_id);
                    }
                }
                var params = (orderData.initData)?orderData.initData:(orderData.webpos_init_data)?JSON.parse(orderData.webpos_init_data):'';
                if(params) {
                    if (params.customer_id) {
                        var customerData = params.customerData;
                        var customerId = customerData.id;
                        var customerDeferred = CustomerFactory.get().load(customerId);
                        customerDeferred.done(function (data) {
                            customerData.addresses = data.addresses;
                            SelectCustomer(customerData);
                            $('#shipping-checkout').val('');
                            $('#billing-checkout').val('');
                        });
                    }
                    CheckoutModel.saveBillingAddress(params.billing_address);
                    CheckoutModel.saveShippingAddress(params.shipping_address);

                    if (params.items && params.items.length > 0) {
                        $.each(params.items, function () {
                            CartModel.addProduct(this);
                        });
                    }

                    DiscountModel.couponCode(params.coupon_code);
                    DiscountModel.cartBaseDiscountAmount(params.config.cart_base_discount_amount);
                    DiscountModel.cartDiscountAmount(params.config.cart_discount_amount);
                    DiscountModel.cartDiscountName(params.config.cart_discount_name);
                    DiscountModel.appliedDiscount(params.config.cart_applied_discount);
                    DiscountModel.appliedPromotion(params.config.cart_applied_promotion);
                    DiscountModel.cartDiscountType(params.config.cart_discount_type);
                    DiscountModel.cartDiscountPercent(params.config.cart_discount_percent);
                    TotalsFactory.get().updateDiscountTotal();

                    if ($('#checkout').length > 0) {
                        $('#checkout').click();
                    }
                    if(Helper.isUseOnline('checkout')){
                        CartModel.saveCartBeforeCheckoutOnline();
                    }else{
                        var viewManager = require('Magestore_Webpos/js/view/layout');
                        viewManager.getSingleton('view/checkout/cart').switchToCheckout();
                    }
                }
            }
        }
    }
);
