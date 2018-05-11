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
         * POS popup plugin by Daniel
         * @param options
         * @returns {$.fn}
         */
        $.fn.posPopup = function(options) {
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
                    container: 'posPopup',
                    content: 'content',
                    title: 'title',
                    submit: 'submit',
                    active: 'active'
                },
                selector: {
                    overlay: '.pos-overlay.main',
                    activeOverlay: '.pos-overlay.active'
                },
                title:'',
                additionalClasses:'',
                onOpen: $.noop,
                onClose: $.noop,
                onSubmit: $.noop,
                hasSubmit: false,
                submitButtonTitle: 'Save',
                overlayDismiss: true,
                useDefault: true
            };

            options = $.extend(defaultOptions, options);

            var self = this;

            /**
             * Open popup
             */
            self.open = function (){
                self.showOverlay();
                this.addClass(options.classes.active);
                options.onOpen.call(null, this);
            }

            /**
             * Close popup
             */
            self.close = function (){
                self.hideOverlay();
                this.removeClass(options.classes.active);
                options.onClose.call(null, this);
            }

            /**
             * Toggle popup
             */
            self.toggle = function () {
                if (this.hasClass(options.classes.active)) {
                    this.close()
                } else {
                    this.open();
                }
            }

            /**
             * Show overlay
             */
            self.showOverlay = function(){
                var overlay = self.parent().find(options.selector.overlay);
                if(overlay.length > 0){
                    overlay.addClass(options.classes.active);
                }else{
                    self.parent().append("<div class='pos-overlay main active'></div>");
                    overlay = self.parent().find('.pos-overlay.main.active');
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
                var overlay = self.parent().find(options.selector.activeOverlay);
                if(overlay.length > 0){
                    overlay.removeClass(options.classes.active);
                }
            }

            /**
             * Submit popup
             */
            self.submit = function () {
                options.onSubmit.call(null, this);
            }

            /**
             * Bind data for each elements
             */
            self.each(function(index, value){
                var element = $(this);
                var init = element.data(options.dataKey.init);
                if(!init){
                    element.addClass(options.additionalClasses);
                    element.data(options.dataKey.init, true);
                    if(options.useDefault == true){
                        element.addClass(options.classes.container);
                        element.wrapInner('<div class="'+options.classes.content+'"></div>');

                        var title = element.data(options.dataKey.title) || options.title;
                        if(title){
                            if(options.hasSubmit){
                                var submitButtonTitle = element.data(options.dataKey.submitButtonTitle) || options.submitButtonTitle;
                                var submitButton = "<button type='button' class='btn-save' data-role='"+options.roles.submit+"''>"+submitButtonTitle+"</button>"
                                element.prepend('<div class="'+options.classes.title+'">' + title + submitButton + '</div>');
                            }else{
                                element.prepend('<div class="'+options.classes.title+'">' + title + '</div>');
                            }
                        }
                    }
                    element.find("[data-role='"+options.roles.submit+"']").click(function(){
                        self.submit();
                    });
                    element.find("[data-role='"+options.roles.dismiss+"']").click(function(){
                        self.close();
                    });
                    self.close();
                }
            });

            return this;
        };
    }
);