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
        'model/appConfig'
    ],
    function ($, AppConfig) {
        'use strict';
        return function (isOnCheckout) {
            var commentPopup = $(AppConfig.ELEMENT_SELECTOR.CART_COMMENT_POPUP);
            if(commentPopup.length > 0){
                commentPopup.removeClass(AppConfig.CLASS.SHOW);
                commentPopup.fadeOut();
                if(isOnCheckout){
                    $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).hide();
                    $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).addClass(AppConfig.CLASS.HIDE);
                }else{
                    $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).show();
                    $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).removeClass(AppConfig.CLASS.HIDE);
                }
                $(AppConfig.ELEMENT_SELECTOR.NOTIFICATION_BELL).show();
                var overlay = commentPopup.parent().find(AppConfig.ELEMENT_SELECTOR.DYNAMIC_OVERLAY);
                if(overlay.length > 0){
                    overlay.removeClass(AppConfig.CLASS.ACTIVE);
                }
            }
        }
    }
);
