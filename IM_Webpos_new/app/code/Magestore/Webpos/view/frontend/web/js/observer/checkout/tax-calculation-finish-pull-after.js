/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/model/checkout/taxcalculator-factory',
    ],
    function ($, ko, Event, TaxCalculatorFactory) {
        "use strict";

        return {
            execute: function() {
                Event.observer('taxrule_finish_pull_after',function(event,data){
                    TaxCalculatorFactory.get().reInitData();
                });
                Event.observer('taxrate_finish_pull_after',function(event,data){
                    TaxCalculatorFactory.get().reInitData();
                });
                Event.observer('taxclass_finish_pull_after',function(event,data){
                    TaxCalculatorFactory.get().reInitData();
                });
            }
        }        
    }
);