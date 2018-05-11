/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    ['jquery', 'Magestore_Webpos/js/model/event-manager'],
    function ($, Event) {
        "use strict";

        /**
         * POS overlay plugin by Daniel
         * @param options
         * @returns {$.fn}
         */
        $.fn.posOverlay = function(options) {
            var defaultOptions = {
                dataKey:{
                    role: 'role'
                },
                roles:{
                    dismiss: 'dismiss'
                },
                classes:{
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
                overlayDismiss: true,
                autoOpen: true
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
                    Event.dispatch('focus_search_input', '');
                }
            }

            if(options.autoOpen == true){
                self.open();
            }
            return this;
        };
    }
);