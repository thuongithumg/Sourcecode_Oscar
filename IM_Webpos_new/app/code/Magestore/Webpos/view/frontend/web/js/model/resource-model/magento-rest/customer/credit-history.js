/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract'
    ],
    function ($, onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            interfaceName:'credit-history',
            initialize: function () {
                this._super();
                this.setSearchApiUrl('/webpos/customers/credit/search');
                this.apiGetCreditBalanceUrl = '/webpos/customers/balance';
                this.setUpdateApiUrl('/webpos/customers/credit/');
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
                this.callRestApi(apiUrl, params, deferred);
            },
        });
    }
);