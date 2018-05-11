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
        'jquery',
        'ko',
        'posComponent',
        'model/checkout/checkout',
        'model/checkout/cart',
        'action/checkout/cart/show-comment-popup',
        'action/checkout/cart/hide-comment-popup',
        'helper/general'
    ],
    function ($,ko, Component, CheckoutModel, CartModel, ShowCommentPopup, HideCommentPopup, Helper) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/cart/commentpopup'
            },
            comment:ko.observable(CheckoutModel.orderComment()),
            initialize: function () {
                this._super();
                var self = this;
                self.isOnCheckout = ko.pureComputed(function(){
                    return (CartModel.currentPage() == CartModel.PAGE.CHECKOUT)?true:false;
                });
                Helper.observerEvent('show_comment_popup', function(){
                    ShowCommentPopup();
                });
                Helper.observerEvent('cart_empty_after', function(){
                    self.comment('');
                });
            },
            setComment: function(data,event){
                this.comment(event.target.value);
            },
            saveComment: function(){
                var self = this;
                CheckoutModel.orderComment(self.comment());
                if(Helper.isOnlineCheckout()){
                    CheckoutModel.saveCustomerNote();
                }
                HideCommentPopup(self.isOnCheckout());
                Helper.dispatchEvent('focus_search_input', '');
            },
            hideCommentPopup: function(){
                var self = this;
                HideCommentPopup(self.isOnCheckout());
                Helper.dispatchEvent('focus_search_input', '');
            }
        });
    }
);