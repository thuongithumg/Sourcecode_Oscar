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
        'ko',
        'ui/components/checkout/checkout/payment',
        'model/checkout/checkout/payment'
    ],
    function (ko, Component, PaymentModel) {
        "use strict";
        return Component.extend({
            items: ko.pureComputed(function(){
                var newItems = [];
                var items = PaymentModel.items();
                var selected = PaymentModel.selectedPayments();
                if(selected && selected.length > 0){
                    ko.utils.arrayForEach(items, function(payment, index){
                        var added = false
                        ko.utils.arrayForEach(selected, function(selectedPayment){
                            if(payment.code == selectedPayment.code){
                                added = true;
                                return false;
                            }
                        });
                        if(added == false && payment.multiable){
                            newItems.push(payment);
                        }
                    });
                }else{
                    newItems = items;
                }
                return newItems;
            }),
            visible: ko.observable(true),
            initObserver: function(){

            }
        });
    }
);
