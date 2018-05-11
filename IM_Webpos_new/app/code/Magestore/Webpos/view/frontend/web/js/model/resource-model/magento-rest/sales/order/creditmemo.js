/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'Magestore_Webpos/js/model/resource-model/magento-rest/abstract',
    ],
    function ($, onlineAbstract) {
        "use strict";

        return onlineAbstract.extend({
            createApiUrl: '/webpos/creditmemo/create',
            interfaceName: 'sales_order_creditmemo',
            
            initialize: function () {
                this._super();
            },
            
            save: function(model, deferred){
                if(!deferred) {
                    deferred = $.Deferred();
                }
                this.callRestApi(this.createApiUrl, 'post', {}, model.getPostData(), deferred, this.interfaceName + '_afterSave');
                return deferred;
            }
        });
    }
);