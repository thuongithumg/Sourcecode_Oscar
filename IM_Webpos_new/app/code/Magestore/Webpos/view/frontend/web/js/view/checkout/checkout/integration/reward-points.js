/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/checkout/checkout/integration/abstract',
        'Magestore_Webpos/js/view/settings/general/rewardpoints/auto-sync-balance',
        'Magestore_Webpos/js/helper/general',
        'Magestore_Webpos/js/model/checkout/cart',
        'Magestore_Webpos/js/model/checkout/integration/reward-points-factory',
    ],
    function ($,ko, Abstract, AutoSyncBalance, Helper, CartModel, RewardPointFactory) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/integration/rewardpoints'
            },
            initialize: function () {
                this._super();
                this.model = RewardPointFactory.get();
                if(!this.addedData){
                    this.initData();
                }
            },
            initData: function(){
                var self = this;
                self.addedData = true;
                self.balance = self.model.balanceAfterApply;
                self.currentAmount = self.model.currentAmount;
                self.useMaxPoint = self.model.useMaxPoint;
                self.updatingBalance = self.model.updatingBalance;
                self.visible = self.model.visible;
                self.canApply = ko.pureComputed(function(){
                    return (self.model.balance() > 0)?true:false;
                });
                self.observerEvent('go_to_checkout_page', $.proxy(function(){
                    if(CartModel.customerId() && Helper.isRewardPointsEnable()){
                        self.updateStorageBalance();
                    }
                }, self));
            },
            pointUseChange: function(el, event){
                var amount = this.getPriceHelper().toNumber(event.target.value);
                amount = (amount > 0)?amount:0;
                this.model.currentAmount(amount);
                if(amount >= this.model.balance()){
                    amount = this.model.balance();
                    this.model.useMaxPoint(true);
                }else{
                    this.model.useMaxPoint(false);
                }
            },
            apply: function(){
                if(Helper.isUseOnline('checkout')){
                    this.model.spendPointOnline();
                }else{
                    this.model.apply();
                }
            },
            updateBalance: function(){
                if(this.updatingBalance() == false){
                    this.model.updateBalance();
                }
            },
            updateStorageBalance: function(){
                this.model.updateStorageBalance();
                var autoSyncBalance = Helper.getLocalConfig(AutoSyncBalance().configPath);
                if(autoSyncBalance == true){
                    this.model.updateBalance();
                }
            }
        });
    }
);
