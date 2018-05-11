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
        'posComponent',
        'helper/general',
        'model/checkout/checkout/payment'
    ],
    function ($, ko, Component, Helper, PaymentModel) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'ui/checkout/checkout/payment',
            },
            items: PaymentModel.items,
            visible: ko.observable(true),
            initialize: function () {
                this._super();
                this.initObserver();
            },
            initObserver: function(){
                var self = this;
                Helper.observerEvent('go_to_checkout_page', function(){
                    PaymentModel.saveDefaultPaymentMethod();
                });
                Helper.observerEvent('reset_payments_data', function(){
                    PaymentModel.renewPayments();
                });
                Helper.observerEvent('save_default_payment', function(){
                    PaymentModel.saveDefaultPaymentMethod();
                });
                PaymentModel.hasSelectedPayment.subscribe(function(selected){
                    self.visible((selected == true)?false:true);
                });
                PaymentModel.showCcForm.subscribe(function(showCCform){
                    self.visible((showCCform == true)?false:true);
                });
            },
            setPaymentMethod: function (data) {
                PaymentModel.addPayment(data);
                Helper.dispatchEvent('hide_payment_popup', '');
            },
            addExtensionMethod: function (data) {
                PaymentModel.addExtensionPayment(data);
            },
            checkPaymentCollection: function () {
                if(this.items().length > 0){
                    return false;
                }
                return true;
            }

        });
    }
);
