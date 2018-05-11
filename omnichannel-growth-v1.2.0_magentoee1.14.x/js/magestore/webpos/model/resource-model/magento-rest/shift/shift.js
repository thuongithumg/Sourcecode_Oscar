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
                this.apiGetShiftDataUrl = "webpos/shift/getData";
                this.apiCloseStoreUrl = "webpos/shift/close";
            },
            getCallBackEvent: function(key){
                switch(key){

                }
            },
            setApiUrl: function(key,value){
                switch(key){
                    case "apiGetShiftDataUrl":
                        this.apiGetShiftDataUrl = value;
                        break;
                    case "apiCloseStoreUrl":
                        this.apiCloseStoreUrl = value;
                        break;
                }
            },
            getApiUrl: function(key){
                switch(key){
                    case "apiGetShiftDataUrl":
                        return this.apiGetShiftDataUrl;
                    case "apiCloseStoreUrl":
                        return this.apiCloseStoreUrl;
                }
            },
            getShiftData: function(params,deferred){
                var apiUrl = this.getApiUrl("apiGetShiftDataUrl");
                this.callApi(apiUrl, params, deferred);
            },
            closeStore: function(params,deferred){
                var apiUrl = this.getApiUrl("apiCloseStoreUrl");
                this.callApi(apiUrl, params, deferred);
            }
        });
    }
);