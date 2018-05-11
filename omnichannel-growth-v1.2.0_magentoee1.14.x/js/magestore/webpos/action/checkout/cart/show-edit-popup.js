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
        return function (event) {
            var editpopup = $(AppConfig.ELEMENT_SELECTOR.EDIT_CART_ITEM_POPUP);
            if(editpopup.length > 0){
                var ptop = event.pageY - 30;
                var viewPortHeight = $(window).height();
                var subheight = viewPortHeight - ptop;
                if (subheight > 442) {
                    editpopup.css({display: "block", position: "absolute", top: ptop + 'px'});
                    editpopup.find(".arrow").css({top: '24px'});
                } else {
                    var disheight = 442 - subheight;
                    var lasttop = ptop - disheight;
                    var aftertop = 24 + disheight;
                    editpopup.css({display: "block", position: "absolute", top: lasttop + 'px'});
                    editpopup.find(".arrow").css({top: aftertop + 'px'});
                }
                if($(AppConfig.ELEMENT_SELECTOR.EDIT_CART_ITEM_QTY_INPUT).length > 0){
                    $(AppConfig.ELEMENT_SELECTOR.EDIT_CART_ITEM_QTY_INPUT).focus();
                }
                $(AppConfig.ELEMENT_SELECTOR.NOTIFICATION_BELL).hide();
                editpopup.posOverlay({
                    onClose: function(){
                        editpopup.hide();
                        $(AppConfig.ELEMENT_SELECTOR.NOTIFICATION_BELL).show();
                    }
                });
            }
        }
    }
);
