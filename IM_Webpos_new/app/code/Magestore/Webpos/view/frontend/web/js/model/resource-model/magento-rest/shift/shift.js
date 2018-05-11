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
            
            interfaceName:'shift',
            initialize: function () {
                this._super();
                this.setCreateApiUrl('/webpos/shifts/save');
                this.setUpdateApiUrl('/webpos/shifts/save/');
                this.setSearchApiUrl('/webpos/shifts/getlist');
            },
            createShift: function(params,deferred, callback){
                var apiUrl =  "/webpos/shifts/save";
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