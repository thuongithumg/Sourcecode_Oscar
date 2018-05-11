/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'jquery',
        'model/resource-model/magento-rest/checkout/abstract'
    ],
    function ($, onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            interfaceName:'cashTransaction',
            initialize: function () {
                this._super();
                this.setCreateApiUrl('/webpos/cash_transaction/save');
                this.setSearchApiUrl('/webpos/shifts/getlist')
            },
            createTransaction: function(params,deferred, callback){
                var apiUrl =  "/webpos/cash_transaction/save";
                var urlParams = {};
                var postData = {};
                postData.cashTransaction = params;
                if(callback){
                    this.callRestApi(apiUrl, "post", urlParams, postData, deferred, callback);
                }
                else{
                    this.callRestApi(apiUrl, "post", urlParams, postData, deferred);
                }

            },


        });
    }
);