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
        'ko',
        'model/sales/order-factory',
        'mage/translate',
        'ui/components/order/action',
        'action/notification/add-notification'
    ],
    function ($, ko, OrderFactory, $t, Component, notification) {
        "use strict";

        return Component.extend({
            inputId: 'input-send-email-order',
            isVisible: ko.observable(false),
            classIn: ko.observable(''),
            stypeDisplay: ko.observable('none'),
            invalidEmail: ko.observable(false),
            customerEmail: ko.observable(''),
            
            defaults: {
                template: 'ui/order/sendemail',
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
                notification($t('An email has been sent for this order!'), true, 'success', 'Success');
                this.display(false);
            },

            validateEmail: function(email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(email);
            }
        });
    }
);