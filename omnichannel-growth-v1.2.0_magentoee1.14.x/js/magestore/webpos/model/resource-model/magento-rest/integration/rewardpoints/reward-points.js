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
                this.setSearchApiUrl('/webpos/integration/getCustomerPoints');
                this.apiGetPointBalanceUrl = "/webpos/integration/getPointBalance";
                this.apiSpendPointUrl = "/webpos/integration/spendPoint";
            },
            getCallBackEvent: function(key){
                switch(key){

                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiGetPointBalanceUrl":
                        this.apiGetPointBalanceUrl = value;
                        break;
                    case "apiSpendPointUrl":
                        this.apiSpendPointUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiGetPointBalanceUrl":
                        return this.apiGetPointBalanceUrl;
                    case "apiSpendPointUrl":
                        return this.apiSpendPointUrl;
                }
            },
            getBalance: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetPointBalanceUrl");
                this.callApi(apiUrl, params, deferred);
            },
            spendPoint: function(params,deferred){
                var apiUrl = this.getApiUrl("apiSpendPointUrl");
                this.callApi(apiUrl, params, deferred);
            }
        });
    }
);