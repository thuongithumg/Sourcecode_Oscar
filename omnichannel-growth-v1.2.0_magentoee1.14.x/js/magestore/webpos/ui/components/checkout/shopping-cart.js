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
define([
    'ko',
    'jquery',
    'posComponent',
    'model/appConfig',
    'eventManager',
    'helper/general',
    'model/checkout/shopping-cart',
    'lib/jquery/posPopup'
], function (ko, $, Component, AppConfig, Event, Helper, ShoppingCartModel) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'ui/checkout/shopping-cart'
        },
        items: ShoppingCartModel.items,
        loading: ShoppingCartModel.loading,
        popupId: "webpos_shopping_cart",
        popup: ko.observable(),
        /**
         * Constructor
         */
        initialize: function () {
            this._super();
            var self = this;
            Event.observer('show_online_shopping_cart_popup', function(){
                self.open();
            });
            Event.observer('close_online_shopping_cart_popup', function(){
                self.close();
            });
        },
        open: function(){
            var self = this;
            var popup = self.popup();
            if(popup){
                popup.open();
            }
        },
        close: function(){
            var self = this;
            var popup = self.popup();
            if(popup){
                popup.close();
            }
        },
        initData: function(element){
            var self = this;
            var popupId = element.id;
            var popup = $('#'+popupId);
            if(popup.length > 0){
                popup.posPopup({
                    title: Helper.__('Shopping Cart'),
                    hasSubmit:true,
                    submitButtonTitle: Helper.__('Update Changes'),
                    onOpen: function(){
                        ShoppingCartModel.refresh();
                    },
                    onSubmit: function(){
                        ShoppingCartModel.submit();
                    }
                });
                self.popup(popup);
            }
        },
        refreshData: function(){
            ShoppingCartModel.refresh();
        }
    });
});