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
        'model/checkout/integration/reward-points'
    ],
    function ($, ko, Abstract, Helper, CartModel, RewardPointModel) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'ui/checkout/checkout/integration/rewardpoints'
            },
            initialize: function () {
                this._super();
                this.model = RewardPointModel;
                this.initData();
            },
            initData: function(){
                var self = this;
                self.balance = self.model.balanceAfterApply;
                self.currentAmount = self.model.currentAmount;
                self.useMaxPoint = self.model.useMaxPoint;
                self.updatingBalance = self.model.updatingBalance;
                self.visible = self.model.visible;
                self.canApply = ko.pureComputed(function(){
                    return (self.model.balance() > 0)?true:false;
                });
                self.observerEvent('go_to_checkout_page', $.proxy(function(){
                    if(CartModel.customerId() && Helper.isRewardPointsEnable() && self.model.canUseExtension()){
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
            useMaxPointChange: function(el, event){
                this.useMaxPoint(event.target.checked);
                this.model.useMaxPoint(event.target.checked);
            },
            apply: function(){
                if(Helper.isOnlineCheckout()){
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
                var autoSyncBalance = Helper.isAutoSyncRewardPointsBalance();
                if(autoSyncBalance == true){
                    this.model.updateBalance();
                }
            }
        });
    }
);
