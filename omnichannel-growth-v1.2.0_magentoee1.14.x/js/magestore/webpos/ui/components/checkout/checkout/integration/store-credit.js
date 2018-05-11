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
        'ui/components/checkout/checkout/integration/abstract',
        'helper/general',
        'model/checkout/cart',
        'model/checkout/integration/store-credit'
    ],
    function ($, ko, Abstract, Helper, CartModel, StoreCreditModel) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'ui/checkout/checkout/integration/storecredit'
            },
            initialize: function () {
                this._super();
                this.model = StoreCreditModel;
                this.initData();
            },
            initData: function(){
                var self = this;
                self.addedData = true;
                self.balance = ko.pureComputed(function(){
                    return self.convertAndFormatPrice(self.model.balanceAfterApply());
                });
                self.currentAmount = ko.pureComputed(function(){
                    return self.convertAndFormatWithoutSymbol(self.model.currentAmount());
                });
                self.canApply = ko.pureComputed(function(){
                    return (self.model.balance() > 0)?true:false;
                });
                self.useMaxPoint = self.model.useMaxPoint;
                self.updatingBalance = self.model.updatingBalance;
                self.visible = self.model.visible;
                self.observerEvent('go_to_checkout_page', $.proxy(function(){
                    if(CartModel.customerId() && Helper.isStoreCreditEnable() && self.model.canUseExtension()){
                        self.updateStorageBalance();
                    }
                }, self));
            },
            pointUseChange: function(el, event){
                var amount = this.getPriceHelper().toNumber(event.target.value);
                amount = (amount > 0)?amount:0;
                event.target.value = amount;
                amount = this.toBasePrice(amount);
                amount = (amount > 0)?amount:0;
                if(amount >= this.model.balance()){
                    amount = this.model.balance();
                    this.model.useMaxPoint(true);
                }else{
                    this.model.useMaxPoint(false);
                }
                this.model.currentAmount(amount);
            },
            useMaxPointChange: function(el, event){
                this.useMaxPoint(event.target.checked);
                this.model.useMaxPoint(event.target.checked);
            },
            apply: function(){
                this.model.apply();
            },
            updateBalance: function(){
                if(this.updatingBalance() == false){
                    this.model.updateBalance();
                }
            },
            updateStorageBalance: function(){
                this.model.updateStorageBalance();
                var autoSyncBalance = Helper.isAutoSyncCreditBalance();
                if(autoSyncBalance == true){
                    this.model.updateBalance();
                }
            }
        });
    }
);
