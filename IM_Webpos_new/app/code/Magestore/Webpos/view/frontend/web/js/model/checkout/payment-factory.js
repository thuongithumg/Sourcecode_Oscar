/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/factory',
        'Magestore_Webpos/js/model/checkout/payment',
        'ko'
    ],
    function(Factory, ModelClass, ko){
         "use strict";
        return {
            noneTakePaymentList: ko.observableArray(['bambora_integration']),

            get: function(){
                var key = 'model/checkout/payment';
                return Factory.getSingleton(key, ModelClass);              
            },
            
            create: function(){
                return ModelClass();
            }
        }
    }
);