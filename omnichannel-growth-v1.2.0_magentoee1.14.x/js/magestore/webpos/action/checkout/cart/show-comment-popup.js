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
                $(AppConfig.ELEMENT_SELECTOR.NOTIFICATION_BELL).hide();
                $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).hide();
                commentPopup.removeClass(AppConfig.CLASS.HIDE);
                commentPopup.fadeIn();
                commentPopup.posOverlay({
                    onClose: function(){
                        commentPopup.fadeOut();
                        $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).show();
                        $(AppConfig.ELEMENT_SELECTOR.SHOW_MENU_BUTTON).removeClass(AppConfig.CLASS.HIDE);
                    }
                });
            }
        }
    }
);
