/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'Magestore_Webpos/js/model/event-manager',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/checkout/cart/totals-factory',
    ],
    function (Event, ViewManager, TotalsFactory) {
        "use strict";

        return {
            execute: function() {
                Event.observer('cart_item_remove_after',function(event,items){
                    if(!items || (items && items.length == 0)){
                        ViewManager.getSingleton('view/checkout/cart/discountpopup').resetData();
                        TotalsFactory.get().updateDiscountTotal();
                    }
                });
            }
        }        
    }
);