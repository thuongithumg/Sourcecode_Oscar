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
        'model/appConfig'
    ],
    function ($, AppConfig) {
        "use strict";

        return {
            suffix:"container",
            /**
             * get container element id
             * @param id
             * @returns {string}
             */
            getContainerId: function(id){
                return id+'_'+this.suffix;
            },
            /**
             * Show menu
             */
            showMenu: function(){
                var self = this;
                var menu = $(AppConfig.ELEMENT_SELECTOR.MENU);
                var menuMask = $(AppConfig.ELEMENT_SELECTOR.MENU_MASK);
                var wrapper = $(AppConfig.ELEMENT_SELECTOR.WRAPPER);
                var notiButton = $(AppConfig.ELEMENT_SELECTOR.NOTIFICATION_BUTTON);
                if(menu.length > 0){
                    if(menu.hasClass(AppConfig.CLASS.MENU_ACTIVE)){
                        menu.removeClass(AppConfig.CLASS.MENU_ACTIVE);
                        if (wrapper.length > 0) {
                            wrapper.removeClass(AppConfig.CLASS.WRAPPER_MENU_ACTIVE);
                        }
                        if (notiButton.length > 0) {
                            notiButton.removeClass(AppConfig.CLASS.WRAPPER_MENU_ACTIVE);
                        }
                        if (menuMask.length > 0) {
                            menuMask.removeClass(AppConfig.CLASS.MENU_ACTIVE);
                        }
                    }else{
                        menu.addClass(AppConfig.CLASS.MENU_ACTIVE);
                        if (wrapper.length > 0) {
                            wrapper.addClass(AppConfig.CLASS.WRAPPER_MENU_ACTIVE);
                        }
                        if (notiButton.length > 0) {
                            notiButton.addClass(AppConfig.CLASS.WRAPPER_MENU_ACTIVE);
                        }
                        if (menuMask.length > 0) {
                            menuMask.addClass(AppConfig.CLASS.MENU_ACTIVE);
                            menuMask.click(function(){
                                menu.removeClass(AppConfig.CLASS.MENU_ACTIVE);
                                menuMask.removeClass(AppConfig.CLASS.MENU_ACTIVE);
                                if (wrapper.length > 0) {
                                    wrapper.removeClass(AppConfig.CLASS.WRAPPER_MENU_ACTIVE);
                                }
                                if (notiButton.length > 0) {
                                    notiButton.removeClass(AppConfig.CLASS.WRAPPER_MENU_ACTIVE);
                                }
                            });
                        }
                    }
                }
            },
            /**
             * Show/hide container
             */
            toggleArea: function (containerId) {
                var self = this;
                var isShowing = false;
                containerId = self.getContainerId(containerId);
                $.each($(AppConfig.ELEMENT_SELECTOR.ACTIVE_CONTAINER),function(){
                    if($(this).attr('id') != containerId){
                        $(this).removeClass(AppConfig.CLASS.ACTIVE);
                    }
                });
                if ($('#' + containerId).length > 0) {
                    $('#' + containerId).toggleClass(AppConfig.CLASS.ACTIVE);
                    if(containerId != AppConfig.MAIN_CONTAINER){
                        $('#'+containerId).addClass(AppConfig.CLASS.POS_CONTAINER);
                    }
                    if($('#' + containerId).hasClass(AppConfig.CLASS.ACTIVE)){
                        isShowing = true;
                    }
                }
                self.showMainContainer();
                return isShowing;
            },
            /**
             * Show main container
             */
            showMainContainer: function(){
                var menu = $(AppConfig.ELEMENT_SELECTOR.MENU);
                var menuMask = $(AppConfig.ELEMENT_SELECTOR.MENU_MASK);
                var wrapper = $(AppConfig.ELEMENT_SELECTOR.WRAPPER);
                var notiButton = $(AppConfig.ELEMENT_SELECTOR.NOTIFICATION_BUTTON);
                if (menu.length > 0) {
                    menu.removeClass(AppConfig.CLASS.MENU_ACTIVE);
                    menu.attr('disabled', false);
                }
                if (wrapper.length > 0) {
                    wrapper.removeClass(AppConfig.CLASS.WRAPPER_MENU_ACTIVE);
                }
                if (notiButton.length > 0) {
                    notiButton.removeClass(AppConfig.CLASS.WRAPPER_MENU_ACTIVE);
                }
                if ($('body').length > 0) {
                    $('body').removeClass(AppConfig.CLASS.HAS_ACTIVE_MENU);
                }
                if (menuMask.length > 0) {
                    menuMask.removeClass(AppConfig.CLASS.MENU_ACTIVE);
                }
            }
        };
    }
);