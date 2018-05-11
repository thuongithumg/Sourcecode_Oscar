/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/model/sales/order-factory',
        'mage/translate',
        'Magestore_Webpos/js/view/sales/order/action',
        
    ],
    function ($, ko, OrderFactory, $t, Component) {
        "use strict";

        return Component.extend({
            inputId: 'input-send-email-order',
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),
            invalidEmail: ko.observable(false),
            customerEmail: ko.observable(''),
            
            defaults: {
                template: 'Magestore_Webpos/sales/order/sendemail',
            },

            initialize: function () {
                this._super();

            },

            display: function (isShow) {
                if(isShow) {
                    if(!this.orderData() || !this.orderData().customer_email) {
                        return;
                    }
                    this.customerEmail(this.orderData().customer_email);
                    this.invalidEmail(false);
                    this.isVisible(true);
                    this.stypeDisplay('block');
                    this.classIn('in');
                    $('.notification-bell').hide();
                    $('#c-button--push-left').hide();
                }else {
                    this.isVisible(false);
                    this.stypeDisplay('none');
                    this.classIn('');
                    $('.notification-bell').show();
                    $('#c-button--push-left').show();
                }
            },
            
            sendEmail: function(){
                var email = $('#'+this.inputId).val();
                if(email){
                    if(!this.validateEmail(email)){
                        this.invalidEmail(true);
                        return;
                    }
                }
                var jsObject = this.getJsObject();
                var deferred = $.Deferred();
                OrderFactory.get().setData(this.orderData()).setMode('online').sendEmail(jsObject, email, deferred);
                this.invalidEmail(false);
                this.addNotification($t('An email has been sent for this order!'), true, 'success', 'Success');
                this.display(false);
            },

            validateEmail: function(email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            }
        });
    }
);