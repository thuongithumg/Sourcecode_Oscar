/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/resource-model/magento-rest/checkout/abstract'
    ],
    function (onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            initialize: function () {
                this._super();
                this.apiGetShippingRateUrl = "/webpos/checkout/getShippingRates";
                this.apiCheckPromotionUrl = "/webpos/checkout/checkPromotion";
                this.apiCreateOrderUrl = "/webpos/checkout/create";
                this.apiSubmitOnlineOrderUrl = "/webpos/checkout/submitonline";
                this.apiSubmitQuoteOnlineUrl = "/webpos/checkout/submitquote";
                this.apiSendEmailUrl = "/webpos/checkout/sendEmail";
                this.apiUnholdUrl = "/webpos/checkout/unhold";
                this.apiSaveQuoteDataUrl = "/webpos/checkout/saveQuoteData";
                this.apiSelectCustomerUrl = "/webpos/checkout/saveCart";
                this.apiApplyCouponUrl = "/webpos/checkout/applyCoupon";
                this.apiCancelCouponUrl = "/webpos/checkout/applyCoupon";
                this.apiPlaceOrderUrl = "/webpos/checkout/placeOrder";
                this.apiSavePaymentMethodUrl = "/webpos/checkout/savePaymentMethod";
                this.apiSaveShippingMethodUrl = "/webpos/checkout/saveShippingMethod";
                this.apiGetCartDataUrl = "/webpos/checkout/getCartData";
                this.apiGetShippingUrl = "/webpos/checkout/getShipping";
                this.apiGetPaymentUrl = "/webpos/checkout/getPayment";
                this.apiGetPaymentRequestUrl = "/webpos/checkout/getPaymentRequest";
            },
            getCallBackEvent: function(key){
                switch(key){
                    case "saveQuoteData":
                        return "save_quote_data_online_after";
                    case "selectCustomer":
                        return "select_customer_online_after";
                    case "applyCoupon":
                        return "apply_coupon_online_after";
                    case "cancelCoupon":
                        return "cancel_coupon_online_after";
                    case "placeOrder":
                        return "place_order_online_after";
                    case "saveShippingMethod":
                        return "save_shipping_method_online_after";
                    case "savePaymentMethod":
                        return "save_payment_method_online_after";
                    case "getCartData":
                        return "get_cart_data_online_after";
                    case "getShipping":
                        return "get_shipping_online_after";
                    case "getPayment":
                        return "get_payment_online_after";
                    case "createOrder":
                        return "sync_offline_order_after";
                    case "getPaymentRequest":
                        return "get_payment_request_after";
                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiSaveQuoteDataUrl":
                        this.apiSaveQuoteDataUrl = value;
                        break;
                    case "apiSelectCustomerUrl":
                        this.apiSelectCustomerUrl = value;
                        break;
                    case "apiApplyCouponUrl":
                        this.apiApplyCouponUrl = value;
                        break;
                    case "apiCancelCouponUrl":
                        this.apiCancelCouponUrl = value;
                        break;
                    case "apiPlaceOrderUrl":
                        this.apiPlaceOrderUrl = value;
                        break;
                    case "apiSavePaymentMethodUrl":
                        this.apiSavePaymentMethodUrl = value;
                        break;
                    case "apiSaveShippingMethodUrl":
                        this.apiSaveShippingMethodUrl = value;
                        break;
                    case "apiGetShippingUrl":
                        this.apiGetShippingUrl = value;
                        break;
                    case "apiGetCartDataUrl":
                        this.apiGetCartDataUrl = value;
                        break;
                    case "apiGetPaymentUrl":
                        this.apiGetPaymentUrl = value;
                        break;
                    case "apiGetShippingRateUrl":
                        this.apiGetShippingRateUrl = value;
                        break;
                    case "apiCheckPromotionUrl":
                        this.apiCheckPromotionUrl = value;
                        break;
                    case "apiCreateOrderUrl":
                        this.apiCreateOrderUrl = value;
                        break;
                    case "apiSubmitOnlineOrderUrl":
                        this.apiSubmitOnlineOrderUrl = value;
                        break;
                    case "apiSubmitQuoteOnlineUrl":
                        this.apiSubmitQuoteOnlineUrl = value;
                        break;
                    case "apiSendEmailUrl":
                        this.apiSendEmailUrl = value;
                        break;
                    case "apiUnholdUrl":
                        this.apiUnholdUrl = value;
                        break;
                    case "apiGetPaymentRequestUrl":
                        this.apiGetPaymentRequestUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiSaveQuoteDataUrl":
                        return this.apiSaveQuoteDataUrl;
                    case "apiSelectCustomerUrl":
                        return this.apiSelectCustomerUrl;
                    case "apiApplyCouponUrl":
                        return this.apiApplyCouponUrl;
                    case "apiCancelCouponUrl":
                        return this.apiCancelCouponUrl;
                    case "apiPlaceOrderUrl":
                        return this.apiPlaceOrderUrl;
                    case "apiGetPaymentRequestUrl":
                        return this.apiGetPaymentRequestUrl;
                    case "apiSavePaymentMethodUrl":
                        return this.apiSavePaymentMethodUrl;
                    case "apiSaveShippingMethodUrl":
                        return this.apiSaveShippingMethodUrl;
                    case "apiGetCartDataUrl":
                        return this.apiGetCartDataUrl;
                    case "apiGetShippingUrl":
                        return this.apiGetShippingUrl;
                    case "apiGetPaymentUrl":
                        return this.apiGetPaymentUrl;
                    case "apiGetShippingRateUrl":
                        return this.apiGetShippingRateUrl;
                    case "apiCheckPromotionUrl":
                        return this.apiCheckPromotionUrl;
                    case "apiCreateOrderUrl":
                        return this.apiCreateOrderUrl;
                    case "apiSubmitOnlineOrderUrl":
                        return this.apiSubmitOnlineOrderUrl;
                    case "apiSubmitQuoteOnlineUrl":
                        return this.apiSubmitQuoteOnlineUrl;
                    case "apiSendEmailUrl":
                        return this.apiSendEmailUrl;
                    case "apiUnholdUrl":
                        return this.apiUnholdUrl;
                }
            },
            getShippingRates: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiGetShippingRateUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            submitOnlineOrder: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiSubmitOnlineOrderUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            submitQuoteOnline: function(params,deferred){
                var apiUrl,
                    urlParams,
                    callBackEvent;
                apiUrl = this.getApiUrl("apiSubmitQuoteOnlineUrl");
                callBackEvent = this.getCallBackEvent("createOrder");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred, callBackEvent);
            },
            sendEmail: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiSendEmailUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            unholdOrder: function(params,deferred){
                var apiUrl,
                    urlParams;
                apiUrl = this.getApiUrl("apiUnholdUrl");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred);
            },
            /**
             * API to get cart information
             * @param params
             * @param deferred
             */
            getCartData: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetCartDataUrl");
                var callBackEvent = this.getCallBackEvent("getCartData");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API to get cart information
             * @param params
             * @param deferred
             */
            getOnlineCart: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetCartDataUrl");
                var callBackEvent = this.getCallBackEvent("getCartData");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API to get list shipping method
             * @param params
             * @param deferred
             */
            getShipping: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetShippingUrl");
                var callBackEvent = this.getCallBackEvent("getShipping");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API to get list payment method
             * @param params
             * @param deferred
             */
            getPayment: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetPaymentUrl");
                var callBackEvent = this.getCallBackEvent("getPayment");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API to check promotion discount - offline checkout
             * @param params
             * @param deferred
             */
            checkPromotion: function(params,deferred){
                var apiUrl = this.getApiUrl("apiCheckPromotionUrl");
                this.callApi(apiUrl, params, deferred);
            },
            /**
             * API to set customer for online quote
             * @param params
             * @param deferred
             */
            selectCustomer: function(params,deferred){
                var apiUrl = this.getApiUrl("apiSelectCustomerUrl");
                var callBackEvent = this.getCallBackEvent("selectCustomer");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API to sync order - offline checkout
             * @param params
             * @param deferred
             */
            createOrder: function(params,deferred){
                var apiUrl,
                    urlParams,
                    callBackEvent;
                apiUrl = this.getApiUrl("apiCreateOrderUrl");
                callBackEvent = this.getCallBackEvent("createOrder");
                urlParams = {};
                this.callRestApi(apiUrl, "post", urlParams, params, deferred, callBackEvent);
            },
            /**
             * API to save shipping method
             * @param params
             * @param deferred
             */
            saveShippingMethod: function(params,deferred){
                var apiUrl = this.getApiUrl("apiSaveShippingMethodUrl");
                var callBackEvent = this.getCallBackEvent("saveShippingMethod");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API to save payment method
             * @param params
             * @param deferred
             */
            savePaymentMethod: function(params,deferred){
                var apiUrl = this.getApiUrl("apiSavePaymentMethodUrl");
                var callBackEvent = this.getCallBackEvent("savePaymentMethod");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API place order
             * @param params
             * @param deferred
             */
            placeOrder: function(params,deferred){
                var apiUrl = this.getApiUrl("apiPlaceOrderUrl");
                var callBackEvent = this.getCallBackEvent("placeOrder");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API get payment request
             * @param params
             * @param deferred
             */
            getPaymentRequest: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetPaymentRequestUrl");
                var callBackEvent = this.getCallBackEvent("getPaymentRequest");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API apply coupon
             * @param params
             * @param deferred
             */
            applyCoupon: function(params,deferred){
                var apiUrl = this.getApiUrl("apiApplyCouponUrl");
                var callBackEvent = this.getCallBackEvent("applyCoupon");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API cancel coupon
             * @param params
             * @param deferred
             */
            cancelCoupon: function(params,deferred){
                var apiUrl = this.getApiUrl("apiCancelCouponUrl");
                var callBackEvent = this.getCallBackEvent("cancelCoupon");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            /**
             * API save quote data
             * @param params
             * @param deferred
             */
            saveQuoteData: function(params,deferred){
                var apiUrl = this.getApiUrl("apiSaveQuoteDataUrl");
                var callBackEvent = this.getCallBackEvent("apiSaveQuoteDataUrl");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
        });
    }
);