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

/*global define*/
define(
    [
        'jquery',
        'model/checkout/checkout',
        'model/checkout/cart',
        'model/checkout/cart/discountpopup',
        'action/checkout/select-customer-checkout',
        'model/sales/order-factory',
        'model/checkout/cart/totals',
        'model/customer/customer-factory',
        'helper/general'
    ],
    function ($, CheckoutModel, CartModel, DiscountModel, SelectCustomer, OrderFactory, Totals, CustomerFactory, Helper) {
        'use strict';
        return function (orderData) {
            if(orderData && orderData.initData){
                if(orderData.status != "notsync"){
                    OrderFactory.get().delete(orderData.entity_id);
                }
            }
            var params = orderData.initData;
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
            Totals.updateDiscountTotal();

            if ($('#checkout').length > 0) {
                $('#checkout').click();
            }
            if(Helper.isOnlineCheckout()){
                if(params.quote_init){
                    CartModel.saveQuoteData(params.quote_init);
                }
                CheckoutModel.loadCurrentQuote();
            }else{
                Helper.dispatchEvent('go_to_checkout_page', '');
            }
        }
    }
);
