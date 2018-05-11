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

            interfaceName:'shift',
            initialize: function () {
                this._super();
                this.setCreateApiUrl('/webpos/session/save');
                this.setUpdateApiUrl('/webpos/session/save/');
                this.setSearchApiUrl('/webpos/session/getlist');
            },
            createShift: function(params,deferred, callback){
                var apiUrl =  "/webpos/session/save";
                var urlParams = {};
                var postData = {};
                postData.shift = params;

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