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
    ['jquery'],
    function ($) {
        "use strict";

        /**
         * POS alert by Daniel
         * @param options
         * @returns {$.posAlert}
         */
        $.posAlert = function(options) {
            var defaultOptions = {
                dataKey:{
                    title: 'title',
                    submitButtonTitle: 'submit-button-title',
                    init: 'init',
                    role: 'role',
                    options: 'options'
                },
                roles:{
                    submit: 'submit',
                    dismiss: 'dismiss'
                },
                classes:{
                    alertPopup: 'posAlert main',
                    container: 'posAlert',
                    content: 'content',
                    title: 'title',
                    submit: 'submit',
                    active: 'active'
                },
                selector: {
                    alertPopup: '.posAlert.main',
                    overlay: '.pos-overlay.main',
                    activeOverlay: '.pos-overlay.active'
                },
                title:'',
                content:'',
                additionalClasses:'',
                onOpen: $.noop,
                onClose: $.noop,
                onSubmit: $.noop,
                hasSubmit: false,
                submitButtonTitle: 'OK',
                overlayDismiss: true
            };

            options = $.extend(defaultOptions, options);

            var self = this;

            /**
             * Open popup
             */
            self.open = function (){
                self.showOverlay();
                self.alertPopup.addClass(options.classes.active);
                options.onOpen.call(null, this);
            }

            /**
             * Close popup
             */
            self.close = function (){
                self.hideOverlay();
                self.alertPopup.removeClass(options.classes.active);
                options.onClose.call(null, this);
            }

            /**
             * Toggle popup
             */
            self.toggle = function () {
                if (self.alertPopup.hasClass(options.classes.active)) {
                    this.close()
                } else {
                    this.open();
                }
            }

            /**
             * Show overlay
             */
            self.showOverlay = function(){
                var overlay = self.alertPopup.parent().find(options.selector.overlay);
                if(overlay.length > 0){
                    overlay.addClass(options.classes.active);
                }else{
                    self.alertPopup.parent().append("<div class='pos-overlay main active'></div>");
                    overlay = self.alertPopup.parent().find('.pos-overlay.main.active');
                }
                if(overlay.length > 0 && options.overlayDismiss == true){
                    overlay.click(function(){
                        self.close();
                    });
                }
            }

            /**
             * Hide overlay
             */
            self.hideOverlay = function(){
                var overlaySelector = options.selector.activeOverlay;
                if($(overlaySelector).length > 0){
                    $(overlaySelector).removeClass(options.classes.active);
                }
            }

            /**
             * Submit popup
             */
            self.submit = function () {
                options.onSubmit.call(null, this);
            }

            var element = $(options.selector.alertPopup);
            if(element.length < 0){
                var elementHtml = "<div class='"+options.classes.alertPopup+"'></div>"
                $('body').append(elementHtml);
                element = $(options.selector.alertPopup);
            }
            if(element.length > 0){
                element.addClass(options.classes.container);
                element.addClass(options.additionalClasses);
                element.html(options.content);
                element.wrapInner('<div class="'+options.classes.content+'"></div>');

                var title = options.title;
                if(title){
                    if(options.hasSubmit){
                        var submitButtonTitle = options.submitButtonTitle;
                        var submitButton = "<button type='button' class='btn-save' data-role='"+options.roles.submit+"''>"+submitButtonTitle+"</button>"
                        element.prepend('<div class="'+options.classes.title+'">' + title + submitButton + '</div>');
                    }else{
                        element.prepend('<div class="'+options.classes.title+'">' + title + '</div>');
                    }
                }
                element.find("[data-role='"+options.roles.submit+"']").click(function(){
                    self.submit();
                });
                element.find("[data-role='"+options.roles.dismiss+"']").click(function(){
                    self.close();
                });
                self.alertPopup = element;
                self.open();
            }
        };

        return $.posAlert;
    }
);