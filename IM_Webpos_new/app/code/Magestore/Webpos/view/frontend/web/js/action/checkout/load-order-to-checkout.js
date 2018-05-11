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
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/action/checkout/select-billing-address',
        'Magestore_Webpos/js/action/checkout/select-shipping-address'
    ],
    function (require, $, CheckoutModel, CartModel, DiscountModel, SelectCustomer,
              ViewManager, OrderFactory, TotalsFactory, CustomerFactory,
              CancelOnhold, Helper, selectBilling, selectShipping) {
        'use strict';
        return function (orderData) {
            if(orderData){
                var viewManager = require('Magestore_Webpos/js/view/layout');
                var params = (orderData.initData)?orderData.initData:(orderData.webpos_init_data)?JSON.parse(orderData.webpos_init_data):'';
                if(params) {
                    if (params.customer_id) {

                        var deferred = CustomerFactory.get().load(params.customer_id);
                        deferred.done(function (currentCustomerData) {
                            SelectCustomer(currentCustomerData);
                            var addressData = currentCustomerData.addresses;
                            var isSetBilling = false;
                            var isSetShipping = false;
                            $.each(addressData, function (index, value) {
                                if (value.default_billing) {
                                    CheckoutModel.saveBillingAddress(value);
                                    viewManager.getSingleton('view/checkout/customer/edit-customer').billingAddressId(value.id);
                                    viewManager.getSingleton('view/checkout/customer/edit-customer').setBillingPreviewData(value);
                                    viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(true);
                                    isSetBilling = true;
                                }
                                if (value.default_shipping) {
                                    CheckoutModel.saveShippingAddress(value);
                                    viewManager.getSingleton('view/checkout/customer/edit-customer').shippingAddressId(value.id);
                                    viewManager.getSingleton('view/checkout/customer/edit-customer').setShippingPreviewData(value);
                                    viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(true);
                                    isSetShipping = true;
                                }
                            });
                            if (!isSetBilling) {
                                selectBilling(0);
                                viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewBilling(false);
                            }

                            if (!isSetShipping) {
                                selectShipping(0);
                                viewManager.getSingleton('view/checkout/customer/edit-customer').isShowPreviewShipping(false);
                            }

                        });
                    } else {
                        CartModel.removeCustomer();
                    }
                    if (typeof params.billing_address !== "undefined") {
                        CheckoutModel.saveBillingAddress(params.billing_address);
                    } else {
                        CheckoutModel.saveBillingAddress({id: 0});
                    }

                    if (typeof params.shipping_address !== "undefined") {
                        CheckoutModel.saveShippingAddress(params.shipping_address);
                    } else {
                        CheckoutModel.saveShippingAddress({id: 0});
                    }

                    CartModel.removeAllCartItem();
                    if (params.items && params.items.length > 0) {
                        $.each(params.items, function () {
                            CartModel.addProduct(this);
                        });
                    }

                    DiscountModel.reset();
                    if (typeof params.coupon_code !== "undefined") {
                        DiscountModel.couponCode(params.coupon_code);
                    } else {
                        DiscountModel.couponCode('');
                    }

                    if (typeof params.config !== "undefined") {
                        DiscountModel.cartBaseDiscountAmount(params.config.cart_base_discount_amount);
                        DiscountModel.cartDiscountAmount(params.config.cart_discount_amount);
                        DiscountModel.cartDiscountName(params.config.cart_discount_name);
                        DiscountModel.appliedDiscount(params.config.cart_applied_discount);
                        DiscountModel.appliedPromotion(params.config.cart_applied_promotion);
                        DiscountModel.cartDiscountType(params.config.cart_discount_type);
                        DiscountModel.cartDiscountPercent(params.config.cart_discount_percent);
                    } else {
                        DiscountModel.cartBaseDiscountAmount(0);
                        DiscountModel.cartDiscountAmount(0);
                        DiscountModel.cartDiscountName('');
                        DiscountModel.appliedDiscount(false);
                        DiscountModel.appliedPromotion(false);
                        DiscountModel.cartDiscountType('');
                        DiscountModel.cartDiscountPercent(0);
                    }
                    TotalsFactory.get().updateDiscountTotal();
                    if(Helper.isUseOnline('checkout')){
                        CartModel.saveCartMultipleOrder(orderData.is_after_place_order_success);

                    }



                }
            }
        }
    }
);
