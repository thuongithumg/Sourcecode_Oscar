/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'ko',
        'Magestore_Webpos/js/view/checkout/checkout/integration/abstract',
        'Magestore_Webpos/js/model/checkout/integration/gift-card-factory',
        'Magestore_Webpos/js/helper/general'
    ],
    function ($, ko, Abstract, GiftCartFactory, Helper) {
        "use strict";
        return Abstract.extend({
            defaults: {
                template: 'Magestore_Webpos/checkout/checkout/integration/giftcard'
            },
            initialize: function () {
                this._super();
                this.model = GiftCartFactory.get();
                this.initData();
            },
            initData: function () {
                var self = this;
                self.balance = ko.pureComputed(function () {
                    return self.convertAndFormatPrice(self.model.balanceAfterApply());
                });
                self.currentAmount = ko.pureComputed(function () {
                    return self.convertAndFormatWithoutSymbol(self.model.currentAmount());
                });
                self.useMaxPoint = self.model.useMaxPoint;
                self.updatingBalance = self.model.updatingBalance;
                self.visible = self.model.visible;
                self.canApply = ko.pureComputed(function () {
                    return (self.model.balance() > 0) ? true : false;
                });
                self.code = self.model.giftcardCode;
                self.customerCodes = self.model.customerCodes;
                self.appliedCards = ko.pureComputed(function () {
                    return self.getAplliedCards();
                });
                self.selectedExistCode = ko.observable();
                self.hasExistedCode = ko.pureComputed(function () {
                    return (self.customerCodes().length > 0) ? true : false;
                });
                self.giftcardLabel = ko.pureComputed(function () {
                    var useOnline = Helper.isUseOnline('checkout');
                    return (useOnline) ? self.__("Customer's gift card") : self.__("Gift card's balance");
                });
                self.isOfflineCheckout = ko.pureComputed(function () {
                    var useOnline = Helper.isUseOnline('checkout');
                    return (useOnline) ? false : true;
                });
                self.existedCodeCaption = Helper.__("-- Select existed code --");
            },
            pointUseChange: function (el, event) {
                var amount = this.getPriceHelper().toNumber(event.target.value);
                amount = (amount > 0) ? amount : 0;
                amount = this.getPriceHelper().toBasePrice(amount);
                this.model.currentAmount(amount);
                // if(amount >= this.model.balance()){
                //     amount = this.model.balance();
                //     this.model.useMaxPoint(true);
                // }else{
                //     this.model.useMaxPoint(false);
                // }
            },
            useMaxPointChange: function (el, event) {
                this.useMaxPoint(event.target.checked);
                this.model.useMaxPoint(event.target.checked);
            },
            apply: function (el) {
                if (Helper.isUseOnline('checkout')) {
                    if (this.model.giftcardCode()) {
                        this.updateBalance();
                    }
                } else {
                    if (el) {
                        this.model.applyGiftCode(this.model.giftcardCode(), this.model.currentAmount());
                    } else {
                        this.model.apply();
                    }
                }
                this.selectedExistCode('');
            },
            updateBalance: function () {
                var self = this;
                if (self.updatingBalance() == false) {
                    try {
                        if (Helper.isUseOnline('checkout')) {
                            if (self.model.giftcardCode()) {
                                self.model.applyGiftcardOnline(self.model.giftcardCode());
                                self.model.giftcardCode('');
                            }
                        } else {
                            self.model.updateBalance();
                        }
                    } catch (error) {
                        console.log(error.message);
                    }
                }
                self.selectedExistCode('');
            },
            saveCode: function (data, event) {
                this.model.giftcardCode(event.target.value);
                this.updateBalance();
            },
            getAplliedCards: function () {
                var self = this;
                var cards = [];
                var appliedCards = self.model.appliedCards();
                ko.utils.arrayForEach(appliedCards, function (giftcard) {
                    cards.push({
                        code: giftcard.code,
                        value: giftcard.value,
                        balance: giftcard.balance,
                        remain: giftcard.remain,
                        usemax: giftcard.usemax,
                        valueFormated: ko.pureComputed(function () {
                            return self.convertAndFormatPrice(giftcard.value())
                        }),
                        balanceFormated: ko.pureComputed(function () {
                            return self.convertAndFormatPrice(giftcard.balance())
                        }),
                        remainFormated: ko.pureComputed(function () {
                            return self.convertAndFormatPrice(giftcard.remain())
                        })
                    });
                });
                return cards;
            },
            removeCard: function (card) {
                this.model.applyGiftCode(card.code, -1);
                this.useMaxPoint(false);
            },
            editCard: function (card) {
                this.model.editCard(card);
            },
            selectExistedCode: function (data, event) {
                var self = this;
                var code = event.target.value;
                self.model.giftcardCode(code);
                self.apply(false);
            }
        });
    }
);
