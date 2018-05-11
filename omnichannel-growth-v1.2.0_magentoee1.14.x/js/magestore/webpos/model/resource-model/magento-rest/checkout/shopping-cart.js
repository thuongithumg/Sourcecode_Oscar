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

define(
    [
        'model/resource-model/magento-rest/checkout/abstract'
    ],
    function (onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            initialize: function () {
                this._super();
                this.apiGetShoppingCartUrl = "/webpos/shopping-cart/items";
                this.apiUpdateShoppingCartItemsUrl = "/webpos/shopping-cart/updateItems";
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