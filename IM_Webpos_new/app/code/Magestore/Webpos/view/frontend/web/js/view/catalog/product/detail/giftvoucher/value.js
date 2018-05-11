/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magestore_Webpos/js/model/giftvoucher/giftvoucher',
        'Magento_Catalog/js/price-utils',
        'mage/translate',
        'Magestore_Webpos/js/helper/alert'
    ],
    function ($,ko, Component, giftvoucher, priceUtils, $t, alert) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magestore_Webpos/catalog/product/detail/giftvoucher/value'
            },
            initialize: function () {
                this._super();
                if (this.priceType() === 'dropdown' && !giftvoucher.giftCardValue()) {
                    var giftAmount = this.giftAmountOption();
                    this.setDropdownPrice(giftAmount[0]);
                }
                if (this.priceType() === 'static' && !giftvoucher.giftCardValue()) {
                    this.setPrice(this.giftAmountStatic());
                }

                if (this.priceType() === 'range' && !giftvoucher.giftCardValue()) {
                    this.setPrice(this.giftAmountFrom());
                }
            },

            giftAmountStatic: giftvoucher.giftAmountStatic,

            giftAmountFrom: giftvoucher.giftAmountFrom,

            giftAmountTo: giftvoucher.giftAmountTo,

            choosePrice: giftvoucher.choosePrice,

            giftAmountOption: giftvoucher.giftAmountOption,

            selectPriceType: ko.computed(function() {
                if ( giftvoucher.selectPriceType() === 1) {
                    return 'static';
                }
                if ( giftvoucher.selectPriceType() === 2) {
                    return 'range';
                }
                if ( giftvoucher.selectPriceType() === 3) {
                    return 'dropdown';
                }
            }),

            giftCardValue: giftvoucher.giftCardValue,
            giftCardPrice: giftvoucher.giftCardPrice,

            priceType: giftvoucher.priceType,

            setPrice: function (data) {
                giftvoucher.giftCardValue(data);
            },

            setDropdownPrice: function (data) {
                giftvoucher.choosePrice(data);
                giftvoucher.giftCardValue(data);
            },

            setRangePrice: function (data,event) {
                if ((event.target.value >= this.giftAmountFrom()) && (event.target.value <= this.giftAmountTo())) {
                    giftvoucher.giftCardValue(event.target.value);
                } else {
                    event.target.value = this.giftAmountFrom();
                    alert({
                        content: $t('Please choose the value correctly!')
                    });
                }
            },


            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, window.webposConfig.priceFormat);
            }

        });
    }
);