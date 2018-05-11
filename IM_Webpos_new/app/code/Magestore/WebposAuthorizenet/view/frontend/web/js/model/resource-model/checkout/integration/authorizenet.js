/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/action/notification/add-notification',
        'mage/translate',
        'Magestore_Webpos/js/helper/alert'
    ],
    function ($, onlineAbstract, CheckoutModel, addNotification, __, Alert) {
        "use strict";
        return onlineAbstract.extend({
            finishPaymentUrl: '/webpos/authorizenet/finishPayment',
            initialize: function () {
                this._super();
            },
            /**
             * Get Authorizenet Token from card data
             *
             * @param data
             * @param deferred
             * @returns deferred
             */
            getToken: function (data, deferred) {
                if (!deferred) {
                    var deferred = $.Deferred();
                }
                var authData = {},
                    cardData = {},
                    secureData = {};
                authData.apiLoginID = data.api_login;
                authData.clientKey = data.client_id;
                cardData.cardNumber = data.additional_data.cc_number;
                cardData.month = data.additional_data.cc_exp_month;
                cardData.year = data.additional_data.cc_exp_year;
                cardData.cardCode = data.additional_data.cc_cid;
                secureData.authData = authData;
                secureData.cardData = cardData;
                /** Get token from authorizenet Accept.js */
                window.Accept.dispatchData(secureData, function (response) {
                    if (!response || (!response.messages && !response.opaqueData)) {
                        Alert({
                            priority: 'danger',
                            title: __('Error'),
                            message: __('An error occurred during processing. Please try again.')
                        });
                        deferred.resolve(null);
                    } else if (response.messages.resultCode == 'Error') {
                        Alert({
                            priority: 'danger',
                            title: __('Error'),
                            message: __(response.messages.message[0].text)
                        });
                        deferred.resolve(null);
                    } else if (response.opaqueData && response.opaqueData.dataValue) {
                        deferred.resolve(response.opaqueData.dataValue);
                    } else {
                        Alert({
                            priority: 'danger',
                            title: __('Error'),
                            message: __('An error occurred during processing. Please try again.')
                        });
                        deferred.resolve(null);
                    }
                });
                this.processResponse(deferred);
                return deferred;
            },
            placePayment: function (params, deferred) {
                if (!deferred) {
                    var deferred = $.Deferred();
                }
                this.callRestApi(this.finishPaymentUrl, "post", {}, params, deferred);
                this.processResponse(deferred);
                return deferred;
            },
            processResponse: function (deferred){
                deferred.fail(function (response) {
                    if (response.responseText) {
                        var error = JSON.parse(response.responseText);
                        if (error.message != undefined) {
                            addNotification(error.message, true, 'danger', 'Error');
                        }else{
                            Alert({
                                priority: 'danger',
                                title: __('Message'),
                                message: __('Something went wrong with the application, please check the exception.log file to see more detail about the error')
                            });
                        }
                    } else {
                        addNotification("Please check your network connection", true, 'danger', 'Error');
                    }
                    CheckoutModel.loading(false);
                });
            }
        });
    }
);