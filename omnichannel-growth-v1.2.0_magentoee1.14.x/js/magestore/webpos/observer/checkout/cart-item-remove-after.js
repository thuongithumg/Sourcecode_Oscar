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
        'eventManager',
        'ui/components/layout',
        'model/checkout/cart/totals'
    ],
    function (Event, ViewManager, TotalsFactory) {
        "use strict";

        return {
            execute: function() {
                Event.observer('cart_item_remove_after',function(event,items){
                    // if(!items || (items && items.length == 0)){
                    //     ViewManager.getSingleton('ui/template/checkout/cart/discountpopup').resetData();
                    //     TotalsFactory.updateDiscountTotal();
                    // }
                });
            }
        }        
    }
);