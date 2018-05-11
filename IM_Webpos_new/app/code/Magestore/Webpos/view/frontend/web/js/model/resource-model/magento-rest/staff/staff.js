/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract'
    ],
    function ($,onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            initialize: function () {
                this._super();;
                this.setChangePassWordApiUrlApiUrl('/webpos/staff/changepassword');
                this.setChangePinApiUrlApiUrl('/webpos/changepin');
            },
            /* Set changePassWord Api Url*/
            setChangePassWordApiUrlApiUrl: function (changePassWord) {
                this.changePassWordApiUrl = changePassWord;
            },
            /* Set changePin Api Url*/
            setChangePinApiUrlApiUrl: function (changePin) {
                this.changePinApiUrl = changePin;
            },
            changePassWord: function(postData, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                if(this.changePassWordApiUrl) {
                    this.callRestApi(
                        this.changePassWordApiUrl,
                        'post',
                        {},
                        postData,
                        deferred
                    );
                }
                return deferred;
            },

            changePin: function(postData, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                if(this.changePinApiUrl) {
                    this.callRestApi(
                        this.changePinApiUrl,
                        'post',
                        {},
                        postData,
                        deferred
                    );
                }
                return deferred;
            },
        });

    }
);