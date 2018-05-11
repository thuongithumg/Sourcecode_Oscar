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
        'Magestore_Webpos/js/helper/price',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/model/sales/order-factory',
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/checkout',
        'Magestore_WebposBambora/js/action/send-request',
        'Magestore_Webpos/js/model/checkout/payment-factory',
        'Magestore_Webpos/js/helper/wait-terminal-loading',
        'Magestore_Webpos/js/helper/alert',
        'mage/translate',
        'Magestore_Webpos/js/model/config/local-config'
    ],
    function ($, ko, modelAbstract, Helper, priceHelper, CheckoutModel, OrderFactory, CheckoutResource, sendRequest, paymentFactory, waitTerminal, Alert, $t, localConfig) {
        "use strict";
        return modelAbstract.extend({

            SALE_REQUEST: 1,
            CHECK_REQUEST: 2,
            CANCEL_REQUEST: 3,

            ERROR_CODE : {
                0: $t("Transaction is approved"),
                1: $t("Transaction is declined"),
                2: $t("Transaction is cancelled"),
                100: $t("General (Unspecified) error "),
                101: $t("Service not responding"),
                102: $t("Service timeout"),
                103: $t("Broken message"),
                104: $t("Illegal field"),
                105: $t("Missing field"),
                106: $t("Field format error"),
                107: $t("Unsupported protocol version"),
                108: $t("No printer"),
                200: $t("Cancelled "),
                201: $t("Parameter update error"),
                202: $t("Software update error"),
                203: $t("Software update will reboot"),
                204: $t("Settlement required"),
                205: $t("Password error"),
                206: $t("Failed to get transaction"),
                207: $t("Parameters invalid"),
                208: $t("General configuration error"),
                209: $t("No communication"),
                210: $t("Card not read (READ CARD command)"),
                211: $t("Card not allowed (READ CARD command)"),
                212: $t("Token already acquired"),
                213: $t("Token already released"),
                214: $t("Token limit reached "),
                215: $t("Acquired tokens exist, please finalize or release."),
                216: $t("Token locked"),
                217: $t("Amount too high"),
                218: $t("Terminal busy"),
                219: $t("Terminal blocked"),
                220: $t("Printer out of paper"),
                221: $t("Invalid user"),
                222: $t("MobilePay reference not found"),
                223: $t("MobilePay transaction already refunded"),
                224: $t("MobilePay technical error."),
                225: $t("MobilePay payment already exist and has been paid."),
                300: $t("Network error"),
                301: $t("Network timeout")
            },


            initialize: function () {
                this._super();
                this.initObserver();
            },
            initObserver: function () {
                var self = this;

                Helper.observerEvent('webpos_place_order_payment_online_before', function (event, orderData) {
                    self.placeBambora(orderData, true);
                });

                Helper.observerEvent('webpos_place_order_before', function (event, orderData) {
                    self.placeBamboraOffline(orderData);
                });

                Helper.observerEvent('cancel_sales_request', function (event, data) {
                    if (window.xhrRequest) {
                        window.xhrRequest.abort();
                    }
                    sendRequest(self.CANCEL_REQUEST, 0 , '');
                    waitTerminal.stopLoader();
                });
            },

            placeBambora: function(orderData, isOnline) {
                var self = this;

                if (orderData.payment) {
                    if (orderData.payment.method_data && Array.isArray(orderData.payment.method_data)) {
                        $.each(orderData.payment.method_data, function (index, method) {
                            if (method.code === 'bambora_integration') {
                                if (!method.reference_number) {
                                    orderData.isPaymentValidated = false;
                                    var amount = method.real_amount;
                                    var currencyCode = window.webposConfig.currentCurrencyCode;

                                    var ip = localConfig.get('hardware/bambora/ip');
                                    var port = localConfig.get('hardware/bambora/port');

                                    if (!ip || !port) {
                                        Alert({
                                            priority:'danger',
                                            title: $t('Error'),
                                            message: $t('Connection failed. Please check the configuration of the payment device integrated with Web POS again.')
                                        });
                                        CheckoutModel.loading(false);
                                        return ;
                                    }

                                    waitTerminal.startLoader(window.webposConfig.bambora_timeout);

                                    sendRequest(self.SALE_REQUEST, amount, currencyCode).done(function (response) {
                                        if (response) {
                                            waitTerminal.stopLoader();
                                            if (response !== "fail") {
                                                var result = JSON.parse(response);
                                                if (result.status === "success") {
                                                    CheckoutModel.loading(true);
                                                    $('#bambora_integration_reference_number').val(result.transactionId);
                                                    if (typeof result.transactionId !== 'undefined' && result.transactionId !== null) {
                                                        method.reference_number = result.transactionId;
                                                    } else {
                                                        CheckoutModel.loading(false);
                                                        orderData.isPaymentValidated = true;
                                                        Alert({
                                                            priority:'danger',
                                                            title: $t('Error'),
                                                            message: $t('Please fill in the reference number before place order.')
                                                        });
                                                        return ;
                                                    }
                                                    if (typeof result.cardType !== 'undefined') {
                                                        method.card_type = result.cardType;
                                                    }
                                                    orderData.payment.method_data[index] = method;
                                                    self.placeOrder(orderData, isOnline);
                                                } else {
                                                    CheckoutModel.loading(false);
                                                    if (result.errorCode !== '' && result.errorCode !== 0) {
                                                        if (typeof self.ERROR_CODE[result.errorCode] !== 'undefined') {
                                                            Alert({
                                                                priority:'danger',
                                                                title: $t('Error'),
                                                                message: self.ERROR_CODE[result.errorCode]
                                                            });
                                                        } else {
                                                            Alert({
                                                                priority:'danger',
                                                                title: $t('Error'),
                                                                message: $t('A transaction error occured. Please swipe your card or check your device\'s connection again.')
                                                            });
                                                        }
                                                    } else {
                                                        Alert({
                                                            priority:'danger',
                                                            title: $t('Error'),
                                                            message: $t('The sale request can not be done. Please try again!')
                                                        });
                                                    }

                                                }
                                            } else {
                                                CheckoutModel.loading(false);
                                                Alert({
                                                    priority:'danger',
                                                    title: $t('Error'),
                                                    message: $t('Connection failed. Please check the configuration of the payment device integrated with Web POS again.')
                                                });
                                            }
                                        }
                                    }).fail(function (response) {
                                        waitTerminal.stopLoader();
                                        CheckoutModel.loading(false);
                                        if (response !== 'timeout') {
                                            Alert({
                                                priority:'danger',
                                                title: $t('Error'),
                                                message: $t('Connection failed. Please make sure the POSHub is running.')
                                            });
                                        } else {
                                            Alert({
                                                priority:'danger',
                                                title: $t('Message'),
                                                message: $t('Sorry. your session has timed out due to a long time of inactivity.')
                                            });
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
            },

            placeBamboraOffline: function(orderData) {
                var self = this;
                if (orderData.sync_params) {
                    if (orderData.sync_params.payment.method_data && Array.isArray(orderData.sync_params.payment.method_data)) {
                        $.each(orderData.sync_params.payment.method_data, function (index, method) {
                            if (method.code === 'bambora_integration') {
                                if (!method.reference_number) {
                                    CheckoutModel.loading(false);
                                    CheckoutModel.placingOrder(false);
                                    $('#checkout-loader').hide();
                                    orderData.isPaymentValidated = false;
                                    var amount = method.real_amount;
                                    var currencyCode = window.webposConfig.currentCurrencyCode;

                                    var ip = localConfig.get('hardware/bambora/ip');
                                    var port = localConfig.get('hardware/bambora/port');

                                    if (!ip || !port) {
                                        Alert({
                                            priority:'danger',
                                            title: $t('Error'),
                                            message: $t('Connection failed. Please check the configuration of the payment device integrated with Web POS again.')
                                        });
                                        CheckoutModel.loading(false);
                                        CheckoutModel.placingOrder(false);
                                        $('#checkout-loader').hide();
                                        return ;
                                    }

                                    waitTerminal.startLoader(window.webposConfig.bambora_timeout);

                                    sendRequest(self.SALE_REQUEST, amount, currencyCode).done(function (response) {
                                        if (response) {
                                            waitTerminal.stopLoader();
                                            if (response !== "fail") {
                                                var result = JSON.parse(response);
                                                if (result.status === "success") {
                                                    CheckoutModel.loading(true);
                                                    $('#bambora_integration_reference_number').val(result.transactionId);
                                                    if (typeof result.transactionId !== 'undefined' && result.transactionId !== null) {
                                                        method.reference_number = result.transactionId;
                                                    } else {
                                                        CheckoutModel.loading(false);
                                                        CheckoutModel.placingOrder(false);
                                                        $('#checkout-loader').hide();
                                                        orderData.isPaymentValidated = true;
                                                        Alert({
                                                            priority:'danger',
                                                            title: $t('Error'),
                                                            message: $t('Please fill in the reference number before place order.')
                                                        });
                                                        return ;
                                                    }
                                                    if (typeof result.cardType !== 'undefined') {
                                                        method.card_type = result.cardType;
                                                    }
                                                    orderData.sync_params.payment.method_data[index] = method;
                                                    self.placeOrder(orderData, false);
                                                } else {
                                                    $('#checkout-loader').hide();
                                                    CheckoutModel.placingOrder(false);
                                                    CheckoutModel.loading(false);
                                                    if (result.errorCode !== '' && result.errorCode !== 0) {
                                                        if (typeof self.ERROR_CODE[result.errorCode] !== 'undefined') {
                                                            Alert({
                                                                priority:'danger',
                                                                title: $t('Error'),
                                                                message: self.ERROR_CODE[result.errorCode]
                                                            });
                                                        } else {
                                                            Alert({
                                                                priority:'danger',
                                                                title: $t('Error'),
                                                                message: $t('A transaction error occured. Please swipe your card or check your device\'s connection again.')
                                                            });
                                                        }
                                                    } else {
                                                        Alert({
                                                            priority:'danger',
                                                            title: $t('Error'),
                                                            message: $t('The sale request can not be done. Please try again!')
                                                        });
                                                    }

                                                }
                                            } else {
                                                $('#checkout-loader').hide();
                                                CheckoutModel.placingOrder(false);
                                                CheckoutModel.loading(false);
                                                Alert({
                                                    priority:'danger',
                                                    title: $t('Error'),
                                                    message: $t('Connection failed. Please check the configuration of the payment device integrated with Web POS again.')
                                                });
                                            }
                                        }
                                    }).fail(function (response) {
                                        waitTerminal.stopLoader();
                                        $('#checkout-loader').hide();
                                        CheckoutModel.placingOrder(false);
                                        CheckoutModel.loading(false);
                                        if (response !== 'timeout') {
                                            Alert({
                                                priority:'danger',
                                                title: $t('Error'),
                                                message: $t('Connection failed. Please make sure the POSHub is running.')
                                            });
                                        } else {
                                            Alert({
                                                priority:'danger',
                                                title: $t('Message'),
                                                message: $t('Sorry. your session has timed out due to a long time of inactivity.')
                                            });
                                        }
                                    });
                                }
                            }
                        });
                    }
                }
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
                    if (orderData.validate && orderData.validate == true) {
                        delete orderData.validate;
                        // change total_paid when total_paid > grand_total
                        if(orderData.webpos_change !== 'undefined' && orderData.webpos_change > 0) {
                            orderData.total_paid -= orderData.webpos_change;
                            orderData.base_total_paid -= orderData.webpos_change;
                        }
                        OrderFactory.get().setData(orderData).setMode('offline').save().done(function (response) {
                            if (response) {
                                CheckoutModel.placeOrder(response);
                                CheckoutModel.syncOrder(response, "checkout");
                                CheckoutModel.placingOrder(false);
                                CheckoutModel.loading(false);
                            }
                        });
                    }
                }
            },
            /**
             *
             */
            placePayment: function (amount, currency) {
                return sendSaleRequest(amount, currency);
            },

            checkConnection: function () {
                
            }
        });
    }
);