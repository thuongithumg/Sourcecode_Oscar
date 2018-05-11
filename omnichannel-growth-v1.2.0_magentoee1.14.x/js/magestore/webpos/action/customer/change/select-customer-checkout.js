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
        'helper/general',
        'model/customer/customer-factory',
        'action/checkout/select-customer-checkout',
        'model/checkout/checkout',
        'model/customer/customer/edit-customer',
        'action/checkout/select-billing-address',
        'action/checkout/select-shipping-address',
        'dataManager'
    ],
    function ($,
              generalHelper,
              CustomerFactory,
              selectCustomerToCheckout,
              CheckoutModel,
              editCustomer,
              selectBilling,
              selectShipping,
              DataManager
    ) {
        'use strict';
         var SelectCustomer = function (object) {
            var customerModel;
            if (generalHelper.isOnlineCheckout()) {
                customerModel = CustomerFactory.get().setMode('online').load(object.id);
            } else {
                customerModel = CustomerFactory.get().setMode('offline').load(object.id);
            }

            customerModel.done(function (data) {
                selectCustomerToCheckout(data);
                var addressData = data.addresses;
                var isSetBilling = false;
                var isSetShipping = false;
                if(addressData && addressData.length > 0) {
                    $.each(addressData, function (index, value) {
                        if (value.default_billing) {
                            if (CheckoutModel) {
                                CheckoutModel.saveBillingAddress(value);
                            } else if(window.WebposCheckoutModel) {
                                window.WebposCheckoutModel.saveBillingAddress(value);
                            }
                            editCustomer.billingAddressId(value.id);
                            editCustomer.setBillingPreviewData(value);
                            editCustomer.isShowPreviewBilling(true);
                            isSetBilling = true;
                        }
                        if (value.default_shipping) {
                            if (CheckoutModel) {
                                CheckoutModel.saveShippingAddress(value);
                            } else if(window.WebposCheckoutModel) {
                                window.WebposCheckoutModel.saveShippingAddress(value);
                            }
                            editCustomer.shippingAddressId(value.id);
                            editCustomer.setShippingPreviewData(value);
                            editCustomer.isShowPreviewShipping(true);
                            isSetShipping = true;
                        }
                    });
                }
                $.each(addressData, function (index, value) {
                    if (value.default_billing) {
                        if (CheckoutModel) {
                            CheckoutModel.saveBillingAddress(value);
                        } else if(window.WebposCheckoutModel) {
                            window.WebposCheckoutModel.saveBillingAddress(value);
                        }
                        editCustomer.billingAddressId(value.id);
                        editCustomer.setBillingPreviewData(value);
                        editCustomer.isShowPreviewBilling(true);
                        isSetBilling = true;
                    }
                    if (value.default_shipping) {
                        if (CheckoutModel) {
                            CheckoutModel.saveShippingAddress(value);
                        } else if(window.WebposCheckoutModel) {
                            window.WebposCheckoutModel.saveShippingAddress(value);
                        }
                        editCustomer.shippingAddressId(value.id);
                        editCustomer.setShippingPreviewData(value);
                        editCustomer.isShowPreviewShipping(true);
                        isSetShipping = true;
                    }
                });
                if (!isSetBilling) {
                    selectBilling(0);
                    editCustomer.isShowPreviewBilling(false);
                }

                if (!isSetShipping) {
                    selectShipping(0);
                    editCustomer.isShowPreviewShipping(false);
                }

                $('#popup-change-customer').removeClass('fade-in');
                $('.pos-overlay').removeClass('active');
                $('.notification-bell').show();
                $('#c-button--push-left').show();
            });
        };
        var defaultCustomer = DataManager.getData('default_customer');
        generalHelper.observerEvent('cart_remove_customer_after', function(){
            if(defaultCustomer){
                SelectCustomer(defaultCustomer);
            }
        });
        return SelectCustomer;
    }
);
