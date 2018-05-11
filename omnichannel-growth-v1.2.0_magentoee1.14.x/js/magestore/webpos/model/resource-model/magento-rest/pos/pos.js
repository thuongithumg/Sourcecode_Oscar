/*
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

define(
    [
        'model/resource-model/magento-rest/abstract'
    ],
    function (onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            initialize: function () {
                this._super();
                this.setSearchApiUrl("/webpos/poslist");
                this.apiAssignUrl = "/webpos/posassign";
            },
            getCallBackEvent: function(key){
                switch(key){
                    case "assign":
                        return "pos_assign_after";
                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "assign":
                        this.apiAssignUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiAssignUrl":
                        return this.apiAssignUrl;
                }
            },
            assign: function(params,deferred){
                var apiUrl = this.getApiUrl("apiAssignUrl");
                var callBackEvent = this.getCallBackEvent("assign");
                this.callApi(apiUrl, params, deferred, callBackEvent);
            }
        });
    }
);