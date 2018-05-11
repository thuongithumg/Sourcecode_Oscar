/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/resource-model/magento-rest/staff/staff'
    ],
    function ($,modelAbstract, restResource) {
        "use strict";
        return modelAbstract.extend({
            mode: "online",
            initialize: function () {
                this._super();
                this.setResource(restResource(), {});
                this.setResourceCollection({});
            },
            changePassWord: function(postData, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                this.getResource().changePassWord(postData,deferred);
                return deferred;
            },

            changePin: function(postData, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                this.getResource().changePin(postData,deferred);
                return deferred;
            }
        });
    }
);