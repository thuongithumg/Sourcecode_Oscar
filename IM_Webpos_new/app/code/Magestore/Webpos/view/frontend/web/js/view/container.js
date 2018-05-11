/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/abstract'
    ],
    function ($,ko, modelAbstract) {
        "use strict";
        return modelAbstract.extend({

            initialize: function () {
                this._super();
            },
            init: function (containerId) {
                var self = this;
                self.elementId = containerId;
            },
            toggleArea: function () {
                var self = this;
                var isShowing = false;
                $.each($('.pos_container.active'),function(){
                    if($(this).attr('id') != self.elementId){
                        $(this).removeClass('active');
                    }
                });
                if ($('#' + self.elementId).length > 0) {
                    if ($('#' + self.elementId).hasClass('active')) {
                        $('#' + self.elementId).removeClass("active");
                    } else {
                        $('#' + self.elementId).addClass("active");
                        isShowing = true;
                    }
                    if(self.elementId != "checkout_container"){
                        $('#'+self.elementId).addClass("pos_container");
                    }
                }
                self.showMainContainer();
                return isShowing;
            },
            showMainContainer: function(){
                if ($('#c-menu--push-left').length > 0) {
                    $('#c-menu--push-left').removeClass("is-active");
                }
                if ($('#o_wrapper').length > 0)
                    $('#o_wrapper').removeClass("has-push-left");
                if ($('#o-wrapper').length > 0) {
                    $('#o-wrapper').removeClass("has-push-left");
                }
                if ($('body').length > 0) {
                    $('body').removeClass("has-active-menu");
                }
                if ($('#c-mask').length > 0) {
                    $('#c-mask').removeClass("is-active");
                }
                if ($('#c-button--push-left').length > 0) {
                    $('#c-button--push-left').attr('disabled', false);
                }
            }

        })
    }
);