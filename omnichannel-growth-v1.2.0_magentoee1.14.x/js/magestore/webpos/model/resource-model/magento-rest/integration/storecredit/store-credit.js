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