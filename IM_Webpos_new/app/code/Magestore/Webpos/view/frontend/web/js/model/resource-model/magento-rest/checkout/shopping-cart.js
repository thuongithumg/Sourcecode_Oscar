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
                this.apiGetShoppingCartUrl = "/webpos/checkout/getCartDataByCustomer";
                this.apiUpdateShoppingCartItemsUrl = "/webpos/checkout/getUpdateOnlineCart/";
            },
            getCallBackEvent: function(key){
                switch(key){
                    case "getShoppingCart":
                        return "get_shopping_cart_online_after";
                    case "apiUpdateShoppingCartItemsUrl":
                        return "update_shopping_cart_items_online_after";
                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiGetShoppingCartUrl":
                        this.apiGetShoppingCartUrl = value;
                        break;
                    case "apiUpdateShoppingCartItemsUrl":
                        this.apiUpdateShoppingCartItemsUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiGetShoppingCartUrl":
                        return this.apiGetShoppingCartUrl;
                    case "apiUpdateShoppingCartItemsUrl":
                        return this.apiUpdateShoppingCartItemsUrl;
                }
            },
            getShoppingCart: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetShoppingCartUrl");
                this.callApi(apiUrl, params, deferred);
            },
            updateShoppingCartItems: function(params,deferred){
                var apiUrl = this.getApiUrl("apiUpdateShoppingCartItemsUrl");
                this.callApi(apiUrl, params, deferred);
            }
        });
    }
);