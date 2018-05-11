/*
 * Created by Wazza Rooney on 9/6/17 3:34 PM
 * Copyright (c) 2017. All rights reserved.
 * Last modified 7/6/17 10:31 AM
 */

define(
    [
        'jquery',
        'ko',
        'ui/components/catalog/product/detail-popup',
        'helper/price'
    ],
    function ($, ko, detailPopup, priceHelper) {
        "use strict";
        ko.bindingHandlers.getConfigData = {
            update: function (element, valueAccessor, allBindings, viewModel, bindingContext) {
                // This will be called once when the binding is first applied to an element,
                // and again whenever any observables/computeds that are accessed change
                // Update the DOM element based on the supplied values here.
                //detailPopup().setAllData();
            }
        };
        return detailPopup.extend({
            defaults: {
                template: 'ui/catalog/product/detail/giftvoucher'
            },
            initialize: function () {
                this._super();
            },
            updatePrice: function (giftvoucherOption) {
                let self = this;

                if (giftvoucherOption.type === 'static') {
                    self.basePriceAmount(giftvoucherOption.value);
                    self.defaultPriceAmount(priceHelper.convertAndFormat(giftvoucherOption.value));
                    return;
                }

                if (giftvoucherOption.type === 'dropdown') {
                    if ($('#giftvoucher-dropdown-price').length) {
                        let price = parseFloat($('#giftvoucher-dropdown-price')[0].value);
                        self.basePriceAmount(price);
                        self.defaultPriceAmount(priceHelper.convertAndFormat(price));
                    }
                    return;
                }

                /* range price */
                if ($('#giftvoucher-amount').length) {
                    let price = parseFloat($('#giftvoucher-amount')[0].value);

                    if (price > giftvoucherOption.to * 1) {
                        price = giftvoucherOption.to * 1;
                    }

                    if (price < giftvoucherOption.from * 1) {
                        price = giftvoucherOption.from * 1;
                    }

                    if (price) {
                        self.basePriceAmount(price);
                        self.defaultPriceAmount(priceHelper.convertAndFormat(price));
                        return;
                    }
                    self.defaultPriceAmount('');
                }
            },
            convertAndFormatPrice: priceHelper.convertAndFormat,
            setCanShip: function (data, event) {
                this.giftvoucherCanShip(event.target.checked);
            },
        });
    }
);