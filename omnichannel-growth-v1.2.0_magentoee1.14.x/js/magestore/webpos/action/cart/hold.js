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

/*global define*/
define(
    [
        'jquery',
        'ko',
        'model/checkout/checkout',
        'model/checkout/cart',
        'model/sales/order-factory',
        'helper/general'
    ],
    function($, ko, CheckoutModel, CartModel, OrderFactory, Helper) {
        'use strict';
        return {
            hold: function(){
                var data = CheckoutModel.getHoldOrderData();
                OrderFactory.get().setData(data).setMode('offline').save().done(function (response) {
                    if(response){
                        CartModel.emptyCart();
                    }
                });
            },
            execute: function(){
                var self = this;
                var quoteInit = CartModel.getQuoteInitParams();
                if(Helper.isOnlineCheckout() && quoteInit.quote_id){
                    CheckoutModel._afterSaveCart(self.hold, '', true);
                }else{
                    self.hold();
                }
            }
        }
    }
);
