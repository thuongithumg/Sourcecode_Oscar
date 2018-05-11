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
                this.apiSaveCartUrl = "/webpos/checkout/saveCart";
                this.apiRemoveCartUrl = "/webpos/checkout/removeCart";
                this.apiRemoveItemUrl = "/webpos/checkout/removeItem";
            },
            getCallBackEvent: function(key){
                switch(key){
                    case "saveCart":
                        return "save_cart_online_after";
                    case "removeCart":
                        return "remove_cart_online_after";
                    case "removeItem":
                        return "remove_item_online_after";
                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiSaveCartUrl":
                        this.apiSaveCartUrl = value;
                        break;
                    case "apiRemoveCartUrl":
                        this.apiRemoveCartUrl = value;
                        break;
                    case "apiRemoveItemUrl":
                        this.apiRemoveItemUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiSaveCartUrl":
                        return this.apiSaveCartUrl;
                    case "apiRemoveCartUrl":
                        return this.apiRemoveCartUrl;
                    case "apiRemoveItemUrl":
                        return this.apiRemoveItemUrl;
                }
            },
            saveCartBeforeCheckout: function(params,deferred){
                var apiUrl = this.getApiUrl("apiSaveCartUrl");
                var callBackEvent = this.getCallBackEvent("saveCart");
                if((typeof params.isSwitchToCheckout !== 'undefined' && params.isSwitchToCheckout === true)) {
                    callBackEvent = '';
                }
                this.callApi(apiUrl, params, deferred, callBackEvent);
            },
            saveCart: function(params,deferred){
                var apiUrl = this.getApiUrl("apiSaveCartUrl");
                this.callApi(apiUrl, params, deferred);
            },
            removeCart: function(params,deferred){
                var apiUrl = this.getApiUrl("apiRemoveCartUrl");
                var callBackEvent = this.getCallBackEvent("removeCart");
                this.callApi(apiUrl, params, deferred, callBackEvent, 'post');
            },
            removeItem: function(params,deferred){
                var apiUrl = this.getApiUrl("apiRemoveItemUrl");
                var callBackEvent = this.getCallBackEvent("removeItem");
                this.callApi(apiUrl, params, deferred, callBackEvent, 'post');
            }
        });
    }
);