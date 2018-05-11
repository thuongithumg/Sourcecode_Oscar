/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/checkout',
        'Magestore_WebposAuthorizenet/js/model/resource-model/checkout/integration/authorizenet'
    ],
    function ($, ko, modelAbstract, Helper, CheckoutModel, CheckoutResource, authorizenetResource) {
        "use strict";
        return modelAbstract.extend({
            initialize: function () {
                this._super();
                this.initObserver();
            },
            initObserver: function () {
                var self = this;

                /**
                 * Add api data for authorizenet payment
                 */
                Helper.observerEvent('add_webpos_payment_before', function (event, data) {
                    self.addPaymentData(data);
                });

                /**
                 * prepare data for authorizenet payment before checkout
                 */
                Helper.observerEvent('webpos_model_checkout_get_payment_data', function (event, data) {
                    self.preparePaymentData(data);
                });
                /**
                 * Place order payment in online mode
                 */
                Helper.observerEvent('webpos_place_order_payment_online_before', function (event, data) {
                    self.processAuthorizePayment(data, true);
                });
                /**
                 * Place order payment in offline mode
                 */
                Helper.observerEvent('webpos_submit_order_payment_online_before', function (event, data) {
                    self.processAuthorizePayment(data, false);
                });
            },
            /**
             * add authorizenet_integration payment data to checkout
             *
             * @param data
             */
            addPaymentData: function (data) {
                if (data.params && data.params.code == 'authorizenet_integration') {
                    if (data.paymentData) {
                        data.paymentData.api_login = data.params.api_login;
                        data.paymentData.client_id = data.params.client_id;
                    }
                }
            },
            /**
             * Prepare authorizenet_integration payment data to checkout
             *
             * @param data
             */
            preparePaymentData: function (data) {
                if (data.data && data.data.code == 'authorizenet_integration') {
                    if (data.selectedPayment) {
                        data.data.api_login = data.selectedPayment.api_login;
                        data.data.client_id = data.selectedPayment.client_id;
                    }
                }
            },
            /**
             * Process authorizenet payment
             *
             * @param orderData
             * @param {boolean} isOnline
             */
            processAuthorizePayment: function (orderData, isOnline) {
                var self = this;
                if (orderData.payment) {
                    if (orderData.payment.method_data && Array.isArray(orderData.payment.method_data)) {
                        orderData.payment.method_data.forEach(function (method) {
                            if (method.code == 'authorizenet_integration') {
                                orderData.isPaymentValidated = false;
                                CheckoutModel.loading(true);
                                self.getPaymentToken(method).done(function (response) {
                                    if (response == null) {
                                        CheckoutModel.loading(false);
                                    } else {
                                        method.additional_data.token = response;
                                        delete method.client_id;
                                        delete method.api_login;
                                        self.placeOrder(orderData, isOnline);
                                    }
                                });
                            }
                        });
                    }
                }
            },
            /**
             * Get Authorizenet Token from card data
             *
             * @param data
             * @param deferred
             * @returns {*}
             */
            getPaymentToken: function (data) {
                return authorizenetResource().getToken(data);
            },
            /**
             * Place authorizenet payment
             *
             * @param orderData
             * @param paymentData
             * @param token
             */
            placePayment: function (orderData, paymentData, token, deferred) {
                var params = {
                    quoteId: orderData.quote_id,
                    token: token,
                    amount: paymentData.base_amount
                };
                return authorizenetResource().setPush(true).placePayment(params, deferred);
            },
            /**
             * Place Order
             *
             * @param orderData
             * @param {boolean} isOnline
             */
            placeOrder: function (orderData, isOnline) {
                var deferred = $.Deferred();
                if (isOnline) {
                    CheckoutResource().setPush(true).setLog(false).placeOrder(orderData, deferred);
                    deferred.done(function (response) {
                        if (response && response.increment_id) {
                            CheckoutModel.placeOrderSuccess(response);
                        }
                    }).always(function () {
                        CheckoutModel.loading(false);
                    });
                } else {
                    CheckoutModel.submitParams(orderData);
                }
            }
        });
    }
);