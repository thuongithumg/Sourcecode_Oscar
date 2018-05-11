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
        'ui/components/catalog/product/detail-popup',
        'helper/price'
    ],
    function ($,ko, detailPopup, priceHelper) {
        "use strict";
        return detailPopup.extend({
            defaults: {
                template: 'ui/catalog/product/detail/storecredit'
            },
            initialize: function () {
                this._super();
            },
            isFixed: function () {
                return this.itemData().storecredit_type == 1 ? true:false;
            },
            isDropdown: function () {
                return this.itemData().storecredit_type == 3 ? true:false;
            },
            isRange: function () {
                return this.itemData().storecredit_type == 2 ? true:false;
            },
            getCreditMin: function(){
                return priceHelper.convertAndFormat(this.itemData().storecredit_min);
            },
            getCreditMax: function(){
                return priceHelper.convertAndFormat(this.itemData().storecredit_max);
            },
            getDropdownValues: function () {
                var self = this;
                var options = [];
                if(self.isDropdown()){
                    var values = this.itemData().customercredit_value;
                    //var rate = this.getProductData().storecredit_rate;
                    var childIds = values.split(',');
                    $.each(childIds, function(index, value){
                        options.push({
                            'key': parseFloat(value),
                            'value': priceHelper.convertAndFormat(parseFloat(value))
                        });
                    });
                }
                return options;
            },
            updatePrice: function (dataObject) {
                var self = this;
                var rate = this.itemData().storecredit_rate;
                var priceUpdate;
                var value;
                if(self.isDropdown()){
                    if($('#storecredit_' + dataObject.entity_id).length > 0){
                        value = parseFloat($('#storecredit_' + dataObject.entity_id).val());
                        priceUpdate = parseFloat(rate) * value;
                        detailPopup().basePriceAmount(priceUpdate);
                        detailPopup().creditValue(value);
                        detailPopup().defaultPriceAmount(priceHelper.convertAndFormat(priceUpdate));
                    }
                } else if (self.isRange()) {
                    if($('#storecredit_' + dataObject.entity_id).length > 0){
                        value = parseFloat($('#storecredit_' + dataObject.entity_id).val());

                        priceUpdate = parseFloat(rate) * value;
                        var priceDisplay;
                        if ($.isNumeric(value)) {
                            priceDisplay = priceHelper.toBasePrice(priceUpdate);
                        } else {
                            priceDisplay = self.itemData().storecredit_min * rate;
                        }
                        detailPopup().basePriceAmount(priceUpdate);
                        detailPopup().creditValue(value);
                        detailPopup().defaultPriceAmount(priceHelper.convertAndFormat(priceDisplay));
                    }
                }
            },
            creditValueFormated: function(value){
                return priceHelper.convertAndFormat(value);
            }
        });
    }
);