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
                this.setSearchApiUrl('/webpos/integration/getCreditList');
                this.apiGetCreditBalanceUrl = "/webpos/integration/getCreditBalance";
                this.apiRefundByCreditUrl = "/webpos/integration/refundByCredit";
            },
            getCallBackEvent: function(key){
                switch(key){

                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiGetCreditBalanceUrl":
                        this.apiGetCreditBalanceUrl = value;
                        break;
                    case "apiRefundByCreditUrl":
                        this.apiRefundByCreditUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiGetCreditBalanceUrl":
                        return this.apiGetCreditBalanceUrl;
                    case "apiRefundByCreditUrl":
                        return this.apiRefundByCreditUrl;
                }
            },
            getBalance: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetCreditBalanceUrl");
                this.callApi(apiUrl, params, deferred);
            },
            refund: function(params,deferred){
                var apiUrl = this.getApiUrl("apiRefundByCreditUrl");
                this.callApi(apiUrl, params, deferred);
            }
        });
    }
);