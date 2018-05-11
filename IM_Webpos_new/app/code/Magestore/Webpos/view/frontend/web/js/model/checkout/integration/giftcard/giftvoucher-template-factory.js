/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/factory',
        'Magestore_Webpos/js/model/checkout/integration/giftcard/giftvoucher-template',
    ],
    function(Factory, ModelClass){
        "use strict";
        return {
            get: function(){
                var key = 'model/checkout/integration/giftcard/giftvoucher-template';
                return Factory.getSingleton(key, ModelClass);
            },

            create: function(){
                return ModelClass();
            }
        }
    }
);