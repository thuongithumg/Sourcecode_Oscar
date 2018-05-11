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
                this.apiRemoveGiftcardUrl = "/webpos/integration/removeGiftcard";
                this.apiApplyGiftcardUrl = "/webpos/integration/applyGiftcard";
                this.apiGetGiftcardBalanceUrl = "/webpos/integration/getGiftcardBalance";
                this.apiRefundGiftcardBalanceUrl = "/webpos/integration/refundGiftcardBalance";
            },
            getCallBackEvent: function(key){
                switch(key){

                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiRemoveGiftcardUrl":
                        this.apiRemoveGiftcardUrl = value;
                        break;
                    case "apiApplyGiftcardUrl":
                        this.apiApplyGiftcardUrl = value;
                        break;
                    case "apiGetGiftcardBalanceUrl":
                        this.apiGetGiftcardBalanceUrl = value;
                        break;
                    case "apiRefundGiftcardBalanceUrl":
                        this.apiRefundGiftcardBalanceUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiRemoveGiftcardUrl":
                        return this.apiRemoveGiftcardUrl;
                    case "apiApplyGiftcardUrl":
                        return this.apiApplyGiftcardUrl;
                    case "apiGetGiftcardBalanceUrl":
                        return this.apiGetGiftcardBalanceUrl;
                    case "apiRefundGiftcardBalanceUrl":
                        return this.apiRefundGiftcardBalanceUrl;
                }
            },
            removeGiftcard: function(params,deferred){
                var apiUrl = this.getApiUrl("apiRemoveGiftcardUrl");
                this.callApi(apiUrl, params, deferred);
            },
            applyGiftcard: function(params,deferred){
                var apiUrl = this.getApiUrl("apiApplyGiftcardUrl");
                this.callApi(apiUrl, params, deferred);
            },
            getBalance: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetGiftcardBalanceUrl");
                this.callApi(apiUrl, params, deferred);
            },
            refundToBalance: function(params,deferred){
                var apiUrl = this.getApiUrl("apiRefundGiftcardBalanceUrl");
                this.callApi(apiUrl, params, deferred);
            },
        });
    }
);