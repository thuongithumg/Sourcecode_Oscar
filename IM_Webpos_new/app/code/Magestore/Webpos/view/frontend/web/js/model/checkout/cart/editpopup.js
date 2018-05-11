/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract',
        'Magestore_Webpos/js/model/checkout/cart'
    ],
    function ($,ko, modelAbstract, CartModel) {
        "use strict";
        return modelAbstract.extend({
            itemId: ko.observable(),
            initialize: function () {
                this._super();
            },
            setItem: function(item){
                this.itemId(item.item_id());
            },
            getItemId: function(){
                return this.itemId();
            },
            getEditingItemId: function(){
                return this.getItemId();
            },
            getData: function(key){
                return CartModel.getItemData(this.getItemId(), key);
            },
            getCartItem: function() {
                return CartModel.getItem(this.getItemId());
            },
            setData: function(key,value){
                CartModel.updateItem(this.getItemId(), key, value);
                if(key == 'qty'){
                    CartModel.collectTierPrice();
                }
            },
            showPopup: function(event){
                var ptop = event.pageY - 30;
                var heightvp = $(window).height();
                var subheight = heightvp - ptop;
                if (subheight > 442) {
                    $("#popup-edit-product").css({display: "block", position: "absolute", top: ptop + 'px'});
                    $("#popup-edit-product .arrow").css({top: '24px'});
                } else {
                    var disheight = 442 - subheight;
                    var lasttop = ptop - disheight;
                    var aftertop = 24 + disheight;
                    $("#popup-edit-product").css({display: "block", position: "absolute", top: lasttop + 'px'});
                    $("#popup-edit-product .arrow").css({top: aftertop + 'px'});
                }
                if($('#editpopup_product_qty').length > 0){
                    $('#editpopup_product_qty').focus();
                }
                $(".wrap-backover").show();
                $('.notification-bell').hide();
                $('#c-button--push-left').hide();
            }
        });
    }
);