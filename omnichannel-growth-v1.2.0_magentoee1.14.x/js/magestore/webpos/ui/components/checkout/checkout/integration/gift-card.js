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
        'model/checkout/integration/gift-card',
        'helper/general'
    ],
    function ($, ko, Abstract, GiftCardModel, Helper) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'ui/checkout/checkout/integration/giftcard'
            },
            initialize: function () {
                this._super();
                this.model = GiftCardModel;
                this.initData();
            },
            initData: function(){
                var self = this;
                self.balance = ko.pureComputed(function(){
                    return self.convertAndFormatPrice(self.model.balanceAfterApply());
                });
                self.currentAmount = ko.pureComputed(function(){
                    return self.convertAndFormatWithoutSymbol(self.model.currentAmount());
                });
                self.useMaxPoint = self.model.useMaxPoint;
                self.updatingBalance = self.model.updatingBalance;
                self.visible = self.model.visible;
                self.canApply = ko.pureComputed(function(){
                    return (self.model.balance() > 0)?true:false;
                });
                self.code = self.model.giftcardCode;
                self.customerCodes = self.model.customerCodes;
                self.appliedCards = ko.pureComputed(function(){
                    return self.getAplliedCards();
                });
                self.selectedExistCode = ko.observable();
                self.hasExistedCode = ko.pureComputed(function(){
                    return (self.customerCodes().length > 0)?true:false;
                });
                self.giftcardLabel = ko.pureComputed(function(){
                   return (Helper.isOnlineCheckout())?self.__("Customer's gift card"):self.__("Gift card's balance");
                });
                self.isOfflineCheckout = ko.pureComputed(function(){
                   return (Helper.isOnlineCheckout())?false:true;
                });
                self.existedCodeCaption = Helper.__("-- Select existed code --");
            },
            pointUseChange: function(el, event){
                var amount = this.getPriceHelper().toNumber(event.target.value);
                amount = (amount > 0)?amount:0;
                amount = this.getPriceHelper().toBasePrice(amount);
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
                var self = this;
                if(Helper.isOnlineCheckout()){
                    if(self.model.giftcardCode()){
                        self.updateBalance();
                    }
                }else{
                    this.model.apply();
                }
                self.selectedExistCode('');
            },
            updateBalance: function(){
                var self = this;
                if(self.updatingBalance() == false){
                    try{
                        if(Helper.isOnlineCheckout()){
                            if(self.model.giftcardCode()){
                                self.model.applyGiftcardOnline(self.model.giftcardCode());
                                self.model.giftcardCode('');
                            }
                        }else{
                            self.model.updateBalance();
                        }
                    }catch(error){
                        console.log(error.message);
                    }
                }
                self.selectedExistCode('');
            },
            saveCode: function(data, event){
                this.model.giftcardCode(event.target.value);
                this.updateBalance();
            },
            getAplliedCards: function(){
                var self = this;
                var cards = [];
                var appliedCards = self.model.appliedCards();
                ko.utils.arrayForEach(appliedCards, function(giftcard) {
                    cards.push({
                        code: giftcard.code,
                        value: giftcard.value,
                        balance: giftcard.balance,
                        remain: giftcard.remain,
                        usemax: giftcard.usemax,
                        valueFormated: ko.pureComputed(function(){
                            return self.convertAndFormatPrice(giftcard.value())
                        }),
                        balanceFormated: ko.pureComputed(function(){
                            return self.convertAndFormatPrice(giftcard.balance())
                        }),
                        remainFormated: ko.pureComputed(function(){
                            return self.convertAndFormatPrice(giftcard.remain())
                        })
                    });
                });
                return cards;
            },
            removeCard: function(card){
                this.model.applyGiftCode(card.code, -1);
                this.useMaxPoint(false);
            },
            editCard: function(card){
                this.model.editCard(card);
            },
            selectExistedCode: function(data, event){
                var self = this;
                var code = event.target.value;
                self.model.giftcardCode(code);
                self.apply();
            }
        });
    }
);
