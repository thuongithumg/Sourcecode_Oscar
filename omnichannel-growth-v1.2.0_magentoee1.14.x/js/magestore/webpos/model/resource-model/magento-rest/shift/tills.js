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
                this.apiAssignUrl = "/webpos/posassign";
            },
            getCallBackEvent: function(key){
                switch(key){

                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiAssignUrl":
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
                this.callApi(apiUrl, params, deferred);
            }
        });
    }
);