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
        'model/appConfig',
        'model/checkout/cart/totals',
        'lib/jquery/posOverlay'
    ],
    function ($, AppConfig, Totals) {
        'use strict';
        return function (isOnCheckout) {
            var discountpopup = $(AppConfig.ELEMENT_SELECTOR.CART_DISCOUNT_POPUP);
            if(discountpopup.length > 0){
                $(AppConfig.ELEMENT_SELECTOR.NOTIFICATION_BELL).hide();
                $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).hide();
                $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).addClass(AppConfig.CLASS.HIDE);
                var totalTop = -100+"vh";
                if($('.'+Totals.ADD_DISCOUNT_TOTAL_CODE).length > 0){
                    var discountTotal = Totals.getTotal(Totals.ADD_DISCOUNT_TOTAL_CODE);
                    if(discountTotal !== false && discountTotal.isVisible() == true){
                        totalTop = $('.'+Totals.ADD_DISCOUNT_TOTAL_CODE).offset().top;
                    }
                }
                if($('.'+Totals.DISCOUNT_TOTAL_CODE).length > 0){
                    var discountTotal = Totals.getTotal(Totals.DISCOUNT_TOTAL_CODE);
                    if(discountTotal !== false && discountTotal.isVisible() == true){
                        totalTop = $('.'+Totals.DISCOUNT_TOTAL_CODE).offset().top;
                    }
                }
                var windowHeight = $(window).height();
                var bottom = windowHeight - totalTop - 30;
                bottom += "px";
                if(isOnCheckout == true){
                    discountpopup.addClass(AppConfig.CLASS.ACTIVE_ON_CHECKOUT);
                }else{
                    discountpopup.removeClass(AppConfig.CLASS.ACTIVE_ON_CHECKOUT);
                }
                discountpopup.find(AppConfig.ELEMENT_SELECTOR.ARROW).css({bottom:bottom});
                discountpopup.show();

                discountpopup.posOverlay({
                    onClose: function(){
                        discountpopup.hide();
                        $(AppConfig.ELEMENT_SELECTOR.NOTIFICATION_BELL).show();
                        $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).show();
                        $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).removeClass(AppConfig.CLASS.HIDE);
                    }
                });
            }
        }
    }
);
