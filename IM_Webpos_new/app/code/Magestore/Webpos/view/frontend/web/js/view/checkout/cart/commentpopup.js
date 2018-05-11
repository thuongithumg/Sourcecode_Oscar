/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/view/layout',
        'Magestore_Webpos/js/model/checkout/checkout',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($,ko, Component, ViewManager, CheckoutModel, Helper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/cart/commentpopup'
            },
            comment:ko.observable(CheckoutModel.orderComment()),
            initialize: function () {
                this._super();
                this.popupEl = "#form-note-order";
                this.isOnCheckout = ko.pureComputed(function(){
                    return ViewManager.getSingleton('view/checkout/cart').isOnCheckoutPage();
                });
                var self = this;
                Helper.observerEvent('cart_empty_after', function(){
                    self.comment('');
                });
                this.hide();
            },
            setComment: function(data,event){
                this.comment(event.target.value);
            },
            saveComment: function(){
                CheckoutModel.orderComment(this.comment());
                this.hide();
                if(Helper.isUseOnline('checkout')){
                    CheckoutModel.saveCustomerNote();
                }
                Helper.dispatchEvent('focus_search_input', '');
            },
            show: function(){
                $(this.popupEl).removeClass("fade");
                $(this.popupEl).addClass("show");
                $(this.popupEl).show();
            },
            hide: function(){
                $(this.popupEl).addClass("fade");
                $(this.popupEl).removeClass("show");
                $(this.popupEl).hide();
                Helper.dispatchEvent('focus_search_input', '');
            },
            isDisplayFullScreen: function () {
                if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
                    return false;
                } else {
                    return true;
                }
            }
        });
    }
);